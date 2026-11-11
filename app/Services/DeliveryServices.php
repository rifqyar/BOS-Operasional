<?php

namespace App\Services;

use App\Http\Traits\LogTraits;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class DeliveryServices
{
    use LogTraits;

    /** Delivery lookup using prod_new. */
    public function searchContainer(string $keyword): array
    {
        return DB::connection('prod_new')->select(
            "SELECT A.*
             FROM t_gatepass A
             INNER JOIN t_spk_cont B ON A.NO_CONT = B.NO_CONT
             WHERE A.NO_CONT LIKE ?
               AND A.JNS_KEGIATAN = '3'
               AND B.STATUS_CONT != 900",
            ['%' . strtoupper(trim($keyword)) . '%']
        );
    }

    public function getGatepass(string $noCont): array
    {
        return DB::connection('prod_new')->select(
            "SELECT * FROM t_gatepass WHERE NO_CONT = ? AND JNS_KEGIATAN = '3'",
            [$noCont]
        );
    }

    public function getDelivery(string $noCont): array
    {
        return DB::connection('prod_new')->select(
            'SELECT * FROM t_op_delivery WHERE NO_CONT = ? AND WK_GATEOUT IS NULL ORDER BY ID DESC',
            [$noCont]
        );
    }

    public function getCondition(): array
    {
        return DB::connection('prod_new')->select('SELECT ID, KONDISI FROM reff_kondisi');
    }

    /** Determine the next screen from current persisted state, not the hidden form field. */
    public function getDetail(string $noCont): array
    {
        $noCont = strtoupper(trim($noCont));
        $gatepass = $this->getGatepass($noCont);
        if (!$gatepass || !$this->searchContainer($noCont)) {
            return ['status' => 0, 'no_cont' => $noCont, 'message' => "NO CONT : {$noCont} NOT FOUND"];
        }

        $deliveries = $this->getDelivery($noCont);
        if (!$deliveries) {
            return [
                'status' => 1,
                'no_cont' => $noCont,
                'ukuran' => $gatepass[0]->UKR_CONT ?? null,
                'delivery' => [],
                'condition' => $this->getCondition(),
            ];
        }

        $delivery = $deliveries[0];
        $ready = !empty($delivery->WK_TRUCKIN)
            && !empty($delivery->WK_CHASSIS)
            && !empty($delivery->WK_INSPECT)
            && empty($delivery->WK_GATEOUT);

        return $ready
            ? ['status' => 2, 'no_cont' => $noCont, 'delivery' => $deliveries, 'condition' => $this->getCondition()]
            : ['status' => 0, 'no_cont' => $noCont, 'message' => 'Container belum memenuhi kondisi Delivery.'];
    }

    /** Apply Truck In or Gate Out and all related updates on prod_new. */
    public function store(array $data, string $username): array
    {
        $db = DB::connection('prod_new');
        $noCont = strtoupper(trim((string) ($data['nomercont'] ?? '')));
        $truck = trim((string) ($data['nomertruck'] ?? ''));
        $gate = strtoupper(trim((string) ($data['gate'] ?? '')));
        $stage = 'begin_transaction';
        $transactionStarted = false;

        try {
            $db->beginTransaction();
            $transactionStarted = true;

            if ($noCont === '' || trim($username) === '') {
                throw new RuntimeException('Nomor container dan operator wajib tersedia.', 422);
            }

            $stage = 'ambil_spk_container';
            $spkCont = $db->table('t_spk_cont')->where('NO_CONT', $noCont)
                ->where('STATUS_CONT', '<>', '900')->orderByDesc('ID')->lockForUpdate()->first();
            if (!$spkCont) {
                throw new RuntimeException('Container tidak ditemukan atau sudah Gate Out.', 404);
            }

            $spk = $db->table('t_spk')->where('ID', $spkCont->ID)->first();
            if (!$spk) {
                throw new RuntimeException('Data SPK container tidak ditemukan.', 404);
            }

            $stage = 'validasi_gatepass';
            $gatepass = $db->table('t_gatepass')->where('NO_CONT', $noCont)
                ->where('JNS_KEGIATAN', '3')->orderByDesc('ID')->first();
            if (!$gatepass) {
                throw new RuntimeException('Gatepass kegiatan Delivery tidak ditemukan.', 404);
            }

            $stage = 'validasi_delivery';
            $delivery = $db->table('t_op_delivery')->where('NO_CONT', $noCont)
                ->whereNull('WK_GATEOUT')->orderByDesc('ID')->lockForUpdate()->first();
            $isTruckIn = !$delivery;
            if ($delivery && (
                empty($delivery->WK_TRUCKIN) || empty($delivery->WK_CHASSIS)
                || empty($delivery->WK_INSPECT) || !empty($delivery->WK_GATEOUT)
            )) {
                throw new RuntimeException('Container belum memenuhi kondisi untuk Gate Out.', 422);
            }

            $now = now();
            $timestamp = $now->format('Y-m-d H:i:s');
            $operation = $db->table('t_operation')->where('NO_CONT', $noCont)
                ->where('NO_SPK', $spk->NO_SPK)->lockForUpdate()->first();
            if (!$operation) {
                throw new RuntimeException('Data operasi container dan SPK tidak ditemukan.', 404);
            }

            $stage = $isTruckIn ? 'validasi_truck_in' : 'validasi_gate_out';
            if ($isTruckIn) {
                if ($truck === '') {
                    throw new RuntimeException('Nomor truck wajib diisi untuk proses Truck In.', 422);
                }
                if (!preg_match('/^GATE [1-6]$/', $gate)) {
                    throw new RuntimeException('Pilih gate Truck In yang valid.', 422);
                }
            } elseif (!preg_match('/^(GATE [1-4]|TIDAK BOLEH GATE OUT)$/', $gate)) {
                throw new RuntimeException('Pilih gate Gate Out yang valid.', 422);
            }

            if ($isTruckIn) {
                $stage = 'ambil_data_kontainer';
                $containerType = $db->table('t_cocostscont')->where('NO_CONT', $noCont)->orderByDesc('ID')->first();
                if (!$containerType) {
                    throw new RuntimeException('Data ukuran dan berat container tidak ditemukan.', 404);
                }

                $stage = 'insert_t_cms';
                $dailyCount = $db->table('t_cms')->whereDate('WK_TRUCKIN', $now->toDateString())->count();
                $dailySecNo = $this->makeDailySecNo($dailyCount + 1);
                $db->table('t_cms')->insert([
                    'NO_CONT' => $noCont,
                    'NO_TRUCK' => $truck,
                    'NO_SPK' => $spk->NO_SPK,
                    'DAILYSECNO' => $dailySecNo,
                    'LINE' => $gate,
                    'STATUS' => $containerType->JNS_CONT,
                    'ISO_CODE' => $containerType->ISO_CODE,
                    'WEIGHT' => $containerType->BRUTO,
                    'YARDPOST' => $spkCont->LOKASI,
                    'WK_TRUCKIN' => $now->format('Y-m-d H:i'),
                ]);

                $stage = 'insert_t_op_delivery';
                $db->table('t_op_delivery')->insert([
                    'NO_CONT' => $noCont,
                    'NO_TRUCK' => $truck,
                    'GATE_T' => $gate,
                    'LOKASI' => $spkCont->LOKASI,
                    'TIER' => $spkCont->TIER,
                    'UKR_CONT' => $spkCont->UKR_CONT,
                    'NO_SPK' => $spk->NO_SPK,
                    'WK_TRUCKIN' => $timestamp,
                ]);

                $stage = 'update_status_container_truck_in';
                $updated = $db->table('t_spk_cont')->where('ID', $spkCont->ID)->where('NO_CONT', $noCont)
                    ->where('STATUS_CONT', '<>', '900')->update(['STATUS_CONT' => '800']);
                if ($updated < 1) {
                    throw new RuntimeException('Status container gagal diperbarui untuk Truck In.', 409);
                }

                $stage = 'update_t_operation_truck_in';
                $updated = $db->table('t_operation')->where('NO_CONT', $noCont)->where('NO_SPK', $spk->NO_SPK)
                    ->update(['WK_TRUCKIN' => $timestamp]);
                if ($updated < 1) {
                    throw new RuntimeException('Waktu Truck In gagal diperbarui pada data operasi.', 409);
                }
            } else {
                $stage = 'ambil_data_eir';
                $container = $db->table('t_spk_cont')->where('ID', $spkCont->ID)->where('NO_CONT', $noCont)->first();
                $containerType = $db->table('t_cocostscont')->where('NO_CONT', $noCont)->orderByDesc('ID')->first();
                if (!$containerType) {
                    throw new RuntimeException('Data tipe container untuk EIR tidak ditemukan.', 404);
                }
                $behandle = $db->table('t_op_behandlein')->where('NO_CONT', $noCont)->orderByDesc('ID')->first();
                $inspection = $db->table('t_op_inspection')->where('NO_CONT', $noCont)->orderByDesc('ID')->first();

                $stage = 'update_denah';
                $db->table('t_denah_lapangan')->where('NM_BLOK', $container->LOKASI)
                    ->where('LEVEL_4', $container->TIER)->update(['USE' => '0']);

                $stage = 'insert_t_eir';
                $db->table('t_eir')->insert([
                    'NO_CONT' => $noCont,
                    'NM_EIR' => $username,
                    'STATUS' => $containerType->JNS_CONT,
                    'FLAT' => $truck !== '' ? $truck : null,
                    'ISO_CODE' => $containerType->ISO_CODE,
                    'BRUTO' => $containerType->BRUTO,
                    'LOKASI_END' => $container->LOKASI . '0' . $container->TIER,
                    'WK_GATEOUT' => $now->format('Y-m-d H:i'),
                    'WK_BEHANDLEIN' => $behandle->W_BEHANDLE ?? null,
                    'WK_START' => $inspection->START_INSP ?? null,
                ]);

                $stage = 'update_status_container_gate_out';
                $updated = $db->table('t_spk_cont')->where('ID', $spkCont->ID)->where('NO_CONT', $noCont)
                    ->where('STATUS_CONT', '<>', '900')->update(['STATUS_CONT' => '900']);
                if ($updated < 1) {
                    throw new RuntimeException('Status container gagal diperbarui untuk Gate Out.', 409);
                }

                $stage = 'update_t_op_delivery_gate_out';
                $updated = $db->table('t_op_delivery')->where('ID', $delivery->ID)->where('NO_CONT', $noCont)
                    ->whereNull('WK_GATEOUT')->update(['GATE_O' => $gate, 'WK_GATEOUT' => $timestamp]);
                if ($updated < 1) {
                    throw new RuntimeException('Data Delivery gagal diperbarui untuk Gate Out.', 409);
                }

                $stage = 'update_t_operation_gate_out';
                $updated = $db->table('t_operation')->where('NO_CONT', $noCont)->where('NO_SPK', $spk->NO_SPK)
                    ->update(['WK_GATEOUT' => $timestamp]);
                if ($updated < 1) {
                    throw new RuntimeException('Waktu Gate Out gagal diperbarui pada data operasi.', 409);
                }

                $stage = 'hapus_job_slip_tunggu';
                $db->table('t_job_slip')->where('NO_CONT', $noCont)->where('STATUS', 'WAITING')
                    ->whereNull('JNS_JOB_SLIP')->delete();

                $stage = 'kosongkan_lokasi_container';
                $db->table('t_spk_cont')->where('ID', $spkCont->ID)->where('NO_CONT', $noCont)
                    ->update(['LOKASI' => null, 'TIER' => null]);
            }

            $db->commit();
            $transactionStarted = false;
            $phase = $isTruckIn ? 'truck_in' : 'gate_out';
            $this->logInfo('delivery', 'Delivery berhasil diproses.', [
                'phase' => $phase,
                'no_container' => $noCont,
                'no_spk' => $spk->NO_SPK,
                'operator' => $username,
                'gate' => $gate,
                'no_truck' => $isTruckIn ? $truck : null,
                'timestamp' => $timestamp,
            ]);

            return [
                'status' => 'success',
                'message' => $isTruckIn ? 'Truck In berhasil diproses.' : 'Gate Out berhasil diproses.',
                'data' => ['phase' => $phase, 'no_cont' => $noCont, 'no_spk' => $spk->NO_SPK],
            ];
        } catch (Throwable $e) {
            try {
                if ($transactionStarted && $db->transactionLevel() > 0) {
                    $db->rollBack();
                }
            } catch (Throwable) {
                // Keep the original operation error for the log and response.
            }
            $this->logError('delivery', 'Gagal memproses Delivery.', [
                'stage' => $stage,
                'no_container' => $noCont,
                'operator' => $username,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'code' => $e->getCode(),
            ]);
            throw $e;
        }
    }

    private function makeDailySecNo(int $sequence): string
    {
        if ($sequence < 10) return 'CMS0000' . $sequence;
        if ($sequence < 100) return 'CMS000' . $sequence;
        if ($sequence < 1000) return 'CMS00' . $sequence;
        if ($sequence < 10000) return 'CMS0' . $sequence;
        return 'CMS' . $sequence;
    }
}
