<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class CopyYardServices
{
    private function legacy()
    {
        return DB::connection('prod');
    }

    public function searchContainer(string $keyword)
    {
        return $this->legacy()->select(
            "SELECT A.*, C.NAMA, D.KETERANGAN
             FROM t_spk_cont A
             INNER JOIN t_spk B ON A.ID = B.ID
             INNER JOIN reff_kode_dok_bc C ON B.JNS_DOK = C.ID
             INNER JOIN reff_status_spk D ON A.STATUS_CONT = D.ID
             WHERE A.NO_CONT LIKE ?
               AND A.STATUS_CONT NOT IN ('000','100','200')
             ORDER BY A.ID DESC
             LIMIT 1",
            ['%' . strtoupper(trim($keyword)) . '%']
        );
    }

    public function getContainer(string $keyword)
    {
        $data = $this->searchContainer($keyword);

        return count($data) ? $data[0] : null;
    }

    /**
     * Re-read container dari legacy untuk memastikan data form tidak dipercaya.
     */
    public function getContainerForUpdate(string $noContainer)
    {
        return $this->legacy()->table('t_spk_cont as A')
            ->join('t_spk as B', 'A.ID', '=', 'B.ID')
            ->join('reff_kode_dok_bc as C', 'B.JNS_DOK', '=', 'C.ID')
            ->join('reff_status_spk as D', 'A.STATUS_CONT', '=', 'D.ID')
            ->select('A.*', 'C.NAMA', 'D.KETERANGAN')
            ->where('A.NO_CONT', strtoupper(trim($noContainer)))
            ->whereNotIn('A.STATUS_CONT', ['000', '100', '200'])
            ->orderByDesc('A.ID')
            ->lockForUpdate()
            ->first();
    }

    /** Resolve the legacy text status through reff_status_spk. */
    public function getStatusByDescription(string $description): ?object
    {
        return $this->legacy()->table('reff_status_spk')
            ->whereRaw('UPPER(TRIM(KETERANGAN)) = ?', [mb_strtoupper(trim($description))])
            ->orderByDesc('ID')
            ->lockForUpdate()
            ->first();
    }

    public function getCurrentLocation($container): string
    {
        if ((string) $container->STATUS_CONT === '900') {
            return 'SAMPAH';
        }

        return trim((string) $container->LOKASI)
            . '0'
            . trim((string) $container->TIER);
    }

    public function getDestination(string $destination): ?object
    {
        $destination = strtoupper(trim($destination));

        if ($destination === '' || $destination === 'SAMPAH') {
            return null;
        }

        // Format legacy: BLOK + "0" + TIER
        return $this->legacy()->selectOne(
            "SELECT *
             FROM t_denah_lapangan
             WHERE CONCAT(NM_BLOK, '0', LEVEL_4) = ?
             LIMIT 1
             FOR UPDATE",
            [$destination]
        );
    }

    public function isLocationOccupied(string $destination, string $noContainer): bool
    {
        return $this->legacy()->selectOne(
            "SELECT NO_CONT
             FROM t_spk_cont
             WHERE CONCAT(LOKASI, '0', TIER) = ?
               AND NO_CONT <> ?
               AND STATUS_CONT IN ('450','460','510','530')
             LIMIT 1
             FOR UPDATE",
            [$destination, $noContainer]
        ) !== null;
    }

    public function getLatestJobSlip(
        string $noContainer,
        string $locationColumn,
        string $locationPattern,
        ?string $status = null
    )
    {
        return $this->legacy()->table('t_job_slip')
            ->where('NO_CONT', $noContainer)
            ->where($locationColumn, 'like', $locationPattern)
            ->when($status !== null, fn ($query) => $query->where('STATUS', $status))
            ->orderByDesc('ID_JOB_SLIP')
            ->lockForUpdate()
            ->first();
    }

    /**
     * Simpan perubahan lokasi ke database legacy prod.
     */
    public function moveContainer(array $data): void
    {
        $db = $this->legacy();

        $db->table('t_spk_cont')
            ->where('NO_CONT', $data['no_cont'])
            ->where('ID', $data['id'])
            ->where('STATUS_CONT', $data['status_before'])
            ->update([
                'LOKASI' => $data['lokasi'],
                'TIER' => $data['tier'],
                'STATUS_CONT' => $data['status_cont'],
                ...(!empty($data['operator']) ? ['OPERATOR' => $data['operator']] : []),
            ]);

        if (!empty($data['job_slip'])) {
            $job = $data['job_slip'];

            $update = [
                'LOKASI_AWAL' => $job['lokasi_awal'] ?? null,
                'TIER_AWAL' => $job['tier_awal'] ?? null,
                'LOKASI_AKHIR' => $job['lokasi_akhir'] ?? null,
                'TIER_AKHIR' => $job['tier_akhir'] ?? null,
                'OPERATOR' => $job['operator'] ?? null,
            ];

            if (array_key_exists('status', $job)) {
                $update['STATUS'] = $job['status'];
            }

            $db->table('t_job_slip')
                ->where('ID_JOB_SLIP', $job['id_job_slip'])
                ->update(array_filter($update, fn ($value) => $value !== null));
        }

        if (!empty($data['behandlein'])) {
            $behandle = $data['behandlein'];
            $db->table('t_op_behandlein')
                ->where('NO_CONT', $data['no_cont'])
                ->where('NO_SPK', $behandle['no_spk'])
                ->update(['ROOM' => $behandle['room']]);
        }
    }

    public function getJobSlipForStatus($container, string $status, string $destination)
    {
        $noContainer = $container->NO_CONT;
        $job = null;

        switch ($status) {
            case '460':
                $job = $this->getLatestJobSlip(
                    $noContainer,
                    'LOKASI_AKHIR',
                    'CIC%'
                );
                break;

            case '510':
                $job = $this->getLatestJobSlip(
                    $noContainer,
                    'LOKASI_AWAL',
                    '1B%'
                );
                break;

            case '530':
                $job = $this->getLatestJobSlip(
                    $noContainer,
                    'LOKASI_AKHIR',
                    '1A%',
                    'DONE'
                );
                break;

            case '520':
                $job = $this->getLatestJobSlip(
                    $noContainer,
                    'LOKASI_AKHIR',
                    'CIC%',
                    'DONE'
                );
                break;

            case '540':
                $job = $this->getLatestJobSlip(
                    $noContainer,
                    'LOKASI_AKHIR',
                    '1A%',
                    'DONE'
                );
                break;

            case '450':
                if (str_starts_with($destination, '1B')) {
                    $job = $this->getLatestJobSlip(
                        $noContainer,
                        'LOKASI_AWAL',
                        '1B%'
                    );
                } else {
                    $job = $this->getLatestJobSlip(
                        $noContainer,
                        'LOKASI_AWAL',
                        'CIC%'
                    );
                }
                break;
        }

        if (!$job) {
            return null;
        }

        return [
            'id_job_slip' => $job->ID_JOB_SLIP,
            'lokasi_awal' => $job->LOKASI_AWAL ?? null,
            'tier_awal' => $job->TIER_AWAL ?? null,
            'lokasi_akhir' => $job->LOKASI_AKHIR ?? null,
            'tier_akhir' => $job->TIER_AKHIR ?? null,
            'status' => in_array($status, ['530', '520', '540'], true)
                ? 'DONE'
                : null,
            'no_spk' => $job->NO_SPK,
        ];
    }

    public function buildMoveData($container, string $action, string $destination): array
    {
        $status = (string) $container->STATUS_CONT;
        $current = $this->getCurrentLocation($container);

        // Aksi buang ke blok sampah.
        if ($action === 'dua') {
            return [
                'no_cont' => $container->NO_CONT,
                'id' => $container->ID,
                'status_before' => $status,
                'lokasi' => 'SAMPAH',
                'tier' => null,
                'status_cont' => '900',
                'job_slip' => null,
            ];
        }

        $destination = strtoupper(trim($destination));

        if (!$this->getDestination($destination)) {
            throw new RuntimeException('Lokasi tujuan tidak terdaftar.', 5);
        }

        if ($this->isLocationOccupied($destination, $container->NO_CONT)) {
            throw new RuntimeException('Lokasi tujuan sudah ditempati container lain.', 4);
        }

        // Legacy transition restrictions.
        $invalid = false;

        if ($status === '460') {
            $invalid = str_starts_with($current, 'CIC')
                && (str_starts_with($destination, '1B') || str_starts_with($destination, '1A'));
        } elseif (in_array($status, ['510', '530', '450'], true)) {
            $invalid =
                (str_starts_with($current, '1B') && str_starts_with($destination, 'CIC'))
                || (str_starts_with($current, '1A') && str_starts_with($destination, 'CIC'))
                || (str_starts_with($current, '1A') && str_starts_with($destination, '1B'));
        } elseif (in_array($status, ['520', '540'], true)) {
            $invalid =
                (str_starts_with($current, '1B') && str_starts_with($destination, 'CIC'))
                || (str_starts_with($current, '1A') && str_starts_with($destination, 'CIC'))
                || (str_starts_with($current, '1B') && str_starts_with($destination, '1A'))
                || (str_starts_with($current, '1A') && str_starts_with($destination, '1B'));
        } elseif ($status === '900') {
            // Status 900 boleh dipindahkan kembali ke lokasi lapangan.
        } else {
            throw new RuntimeException('Status container tidak mendukung proses CopyYard.', 8);
        }

        if ($invalid) {
            throw new RuntimeException('Perpindahan lokasi tidak diizinkan untuk status container ini.', 7);
        }

        $tier = strtoupper(substr($destination, -1));
        $length = strlen($destination);
        $blockLength = in_array($status, ['460', '520'], true)
            ? 5
            : ($length > 8 ? 7 : 6);
        $block = strtoupper(substr($destination, 0, $blockLength));

        $newStatus = $status === '900' ? '450' : $status;

        $jobSlip = $this->getJobSlipForStatus($container, $status, $destination);

        if ($status !== '900' && !$jobSlip) {
            throw new RuntimeException('Job Slip terkait tidak ditemukan untuk memperbarui lokasi.', 9);
        }

        if ($status === '450' && $jobSlip) {
            if (str_starts_with($destination, '1B')) {
                $jobSlip['lokasi_awal'] = $block;
                $jobSlip['tier_awal'] = $tier;
            } else {
                $jobSlip['lokasi_akhir'] = $block;
                $jobSlip['tier_akhir'] = $tier;
            }
        } elseif ($status === '460' && $jobSlip) {
            $jobSlip['lokasi_akhir'] = $block;
            $jobSlip['tier_akhir'] = $tier;
        } elseif ($status === '510' && $jobSlip) {
            $jobSlip['lokasi_awal'] = $block;
            $jobSlip['tier_awal'] = $tier;
        } elseif (in_array($status, ['530', '520', '540'], true) && $jobSlip) {
            $jobSlip['lokasi_akhir'] = $block;
            $jobSlip['tier_akhir'] = $tier;
        }

        $behandleIn = $status === '510' && $jobSlip
            ? ['no_spk' => $jobSlip['no_spk'], 'room' => $block . '0' . $tier]
            : null;

        return [
            'no_cont' => $container->NO_CONT,
            'id' => $container->ID,
            'status_before' => $status,
            'lokasi' => $block,
            'tier' => $tier,
            'status_cont' => $newStatus,
            'job_slip' => $jobSlip,
            'behandlein' => $behandleIn,
        ];
    }
}
