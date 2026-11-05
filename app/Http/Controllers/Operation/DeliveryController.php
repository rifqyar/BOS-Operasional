<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogTraits;
use App\Services\DeliveryServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

class DeliveryController extends Controller
{
    use LogTraits;

    protected string $logChannel = 'delivery';

    public function __construct(
        protected DeliveryServices $deliveryService
    ) {
    }

    /**
     * Search container Delivery.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'search_cont2' => ['required', 'string', 'max:50'],
            ]);

            $noCont = strtoupper(trim($validated['search_cont2']));

            $searchResult = $this->deliveryService->searchContainer($noCont);

            if (empty($searchResult)) {
                $this->logWarning(
                    $this->logChannel,
                    'Container Delivery tidak ditemukan.',
                    ['no_cont' => $noCont]
                );

                return response()->json([
                    'status' => 'info',
                    'message' => "NO CONT : {$noCont} NOT FOUND",
                    'data' => null,
                ], 404);
            }

            $result = $this->deliveryService->getDetail($noCont);

            if (($result['status'] ?? 0) === 0) {
                return response()->json([
                    'status' => 'info',
                    'message' => $result['message'] ?? "NO CONT : {$noCont} NOT FOUND",
                    'data' => null,
                ], 404);
            }

            $html = view(
                'livewire.partials.delivery.form',
                ['result' => $result]
            )->render();

            $this->logInfo(
                $this->logChannel,
                'Search Delivery berhasil.',
                [
                    'no_cont' => $noCont,
                    'delivery_status' => $result['status'],
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Data Delivery berhasil ditemukan.',
                'data' => $html,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'info',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'data' => null,
            ], 422);
        } catch (Throwable $e) {
            $this->logError(
                $this->logChannel,
                'Gagal melakukan search Delivery.',
                [
                    'search_cont2' => $request->input('search_cont2'),
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mencari container Delivery.',
                'data' => null,
            ], 500);
        }
    }

    /** Explicit detail endpoint retained in routes/web.php. */
    public function detail(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nomercont' => ['required', 'string', 'max:50'],
            ]);
            $noCont = strtoupper(trim($validated['nomercont']));
            $result = $this->deliveryService->getDetail($noCont);

            if (($result['status'] ?? 0) === 0) {
                return response()->json([
                    'status' => 'info',
                    'message' => $result['message'],
                    'data' => null,
                ], 404);
            }

            $html = view('livewire.partials.delivery.form', ['result' => $result])->render();
            $this->logInfo($this->logChannel, 'Detail Delivery berhasil diambil.', [
                'no_container' => $noCont,
                'delivery_phase' => $result['status'] === 1 ? 'truck_in' : 'gate_out',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Detail Delivery berhasil diambil.',
                'data' => $html,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'info',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'data' => null,
            ], 422);
        } catch (Throwable $e) {
            $this->logError($this->logChannel, 'Gagal mengambil detail Delivery.', [
                'no_container' => $request->input('nomercont'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil detail Delivery.',
                'data' => null,
            ], 500);
        }
    }

    /** Store either Truck In or Gate Out after rechecking the current prod state. */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nomercont' => ['required', 'string', 'max:50'],
                'gate' => ['nullable', 'string', 'max:50'],
                'nomertruck' => ['nullable', 'string', 'max:50'],
            ]);

            $username = (string) (Auth::user()?->username
                ?? Auth::user()?->user_name
                ?? session('USERLOGIN', ''));
            $result = $this->deliveryService->store($validated, $username);

            return response()->json([
                'status' => $result['status'],
                'message' => $result['message'],
                'data' => $result['data'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'info',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
                'data' => null,
            ], 422);
        } catch (Throwable $e) {
            $statusCode = (int) $e->getCode();
            $statusCode = in_array($statusCode, [404, 409, 422], true) ? $statusCode : 500;

            return response()->json([
                'status' => $statusCode === 500 ? 'error' : 'info',
                'message' => $statusCode === 500
                    ? 'Terjadi kesalahan saat memproses Delivery.'
                    : $e->getMessage(),
                'data' => null,
            ], $statusCode);
        }
    }
}
