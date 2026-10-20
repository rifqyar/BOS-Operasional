<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait LogTraits
{
    public function logInfo(string $channel, string $message, $context = [])
    {
        Log::channel('' . $channel)->info($message, array_merge($context, ['timestamp' => Carbon::now()]));
    }

    public function logError(string $channel, string $message, $context = [])
    {
        Log::channel('' . $channel)->error($message, array_merge($context, ['timestamp' => Carbon::now()]));
    }

    public function logWarning(string $channel, string $message, $context = [])
    {
        Log::channel('' . $channel)->warning($message, array_merge($context, ['timestamp' => Carbon::now()]));
    }

    public function logDb($response = null, $service = null, $payload = null, $user_id = null, $success = 0)
    {
        DB::beginTransaction();
        try {
            DB::commit();
        } catch (Exception $th) {
            DB::rollBack();
            $this->logError('logdb', 'Gagal menyimpan log ke database', [
                'error' => $th->getMessage(),
                'service' => $service,
                'user_id' => $user_id
            ]);
        }

    }
}
