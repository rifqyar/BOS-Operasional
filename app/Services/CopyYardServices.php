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

    private function oracle()
    {
        return DB::connection('prod_new');
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
        return $this->legacy()->selectOne(
            "SELECT A.*, C.NAMA, D.KETERANGAN
             FROM t_spk_cont A
             INNER JOIN t_spk B ON A.ID = B.ID
             INNER JOIN reff_kode_dok_bc C ON B.JNS_DOK = C.ID
             INNER JOIN reff_status_spk D ON A.STATUS_CONT = D.ID
             WHERE A.NO_CONT = ?
               AND A.STATUS_CONT NOT IN ('000','100','200')
             ORDER BY A.ID DESC
             LIMIT 1",
            [$noContainer]
        );
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
        $tier = substr($destination, -1);
        $block = substr($destination, 0, -1);

        return $this->legacy()->selectOne(
            "SELECT *
             FROM t_denah_lapangan
             WHERE CONCAT(NM_BLOK, '0', LEVEL_4) = ?
             LIMIT 1",
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
             LIMIT 1",
            [$destination, $noContainer]
        ) !== null;
    }

    public function getLatestJobSlip(string $noContainer, string $condition, array $bindings)
    {
        // $condition hanya dipilih dari kondisi internal di bawah, bukan input user.
        $sql = "SELECT *
                FROM t_job_slip
                WHERE NO_CONT = ?
                  AND {$condition}
                ORDER BY ID_JOB_SLIP DESC
                LIMIT 1";

        return $this->legacy()->selectOne($sql, array_merge([$noContainer], $bindings));
    }

    /**
     * Simpan perubahan lokasi ke Oracle.
     * Asumsi struktur t_spk_cont/t_job_slip di Oracle sama dengan legacy.
     */
    public function moveContainer(array $data): void
    {
        $oracle = $this->oracle();

        $oracle->table('t_spk_cont')
            ->where('NO_CONT', $data['no_cont'])
            ->where('ID', $data['id'])
            ->update([
                'LOKASI' => $data['lokasi'],
                'TIER' => $data['tier'],
                'STATUS_CONT' => $data['status_cont'],
            ]);

        if (!empty($data['job_slip'])) {
            $job = $data['job_slip'];

            $update = [
                'LOKASI_AWAL' => $job['lokasi_awal'] ?? null,
                'TIER_AWAL' => $job['tier_awal'] ?? null,
                'LOKASI_AKHIR' => $job['lokasi_akhir'] ?? null,
                'TIER_AKHIR' => $job['tier_akhir'] ?? null,
            ];

            if (array_key_exists('status', $job)) {
                $update['STATUS'] = $job['status'];
            }

            $oracle->table('t_job_slip')
                ->where('ID_JOB_SLIP', $job['id_job_slip'])
                ->update(array_filter($update, fn ($value) => $value !== null));
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
                    "LOKASI_AKHIR LIKE ?",
                    ['CIC%']
                );
                break;

            case '510':
                $job = $this->getLatestJobSlip(
                    $noContainer,
                    "LOKASI_AWAL LIKE ?",
                    ['1B%']
                );
                break;

            case '530':
                $job = $this->getLatestJobSlip(
                    $noContainer,
                    "LOKASI_AKHIR LIKE ?",
                    ['1A%']
                );
                break;

            case '520':
                $job = $this->getLatestJobSlip(
                    $noContainer,
                    "LOKASI_AKHIR LIKE ?",
                    ['CIC%']
                );
                break;

            case '540':
                $job = $this->getLatestJobSlip(
                    $noContainer,
                    "LOKASI_AKHIR LIKE ?",
                    ['1A%']
                );
                break;

            case '450':
                if (str_starts_with($destination, '1B')) {
                    $job = $this->getLatestJobSlip(
                        $noContainer,
                        "LOKASI_AWAL LIKE ?",
                        ['1B%']
                    );
                } else {
                    $job = $this->getLatestJobSlip(
                        $noContainer,
                        "LOKASI_AWAL LIKE ?",
                        ['CIC%']
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

        $tier = substr($destination, -1);
        $block = substr($destination, 0, -1);

        $newStatus = $status === '900' ? '450' : $status;

        $jobSlip = $this->getJobSlipForStatus($container, $status, $destination);

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

        return [
            'no_cont' => $container->NO_CONT,
            'id' => $container->ID,
            'lokasi' => $block,
            'tier' => $tier,
            'status_cont' => $newStatus,
            'job_slip' => $jobSlip,
        ];
    }
}