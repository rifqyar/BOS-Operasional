<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Throwable;

class MonitoringReeferServices
{
    /**
     * Mencari daftar reefer yang pernah di-Plugin, seperti
     * searchmonitorreefer() pada legacy.
     */
    public function searchReefer(string $keyword): array
    {
        return $this->normalizeRows(DB::connection('prod_new')->select(
            "SELECT ID, NO_CONT, WAKTU, NO_SPK, TEMPERATURE_MONITOR
             FROM (
                SELECT
                    R.ID,
                    R.NO_CONT,
                    R.WAKTU,
                    R.NO_SPK,
                    R.TEMPERATURE_MONITOR,
                    ROW_NUMBER() OVER (
                        PARTITION BY R.NO_CONT
                        ORDER BY R.ID DESC
                    ) AS RN
                FROM T_OP_REEFER R
                WHERE R.WAKTU IS NOT NULL
                  AND R.WAKTU_END IS NULL
                  AND UPPER(R.NO_CONT) LIKE ?
             ) ACTIVE_REEFER
             WHERE RN = 1
             ORDER BY ID DESC",
            ['%' . strtoupper(trim($keyword)) . '%']
        ));
    }

    /**
     * Mengambil data container yang masih plugin.
     * Mengikuti serchmonitor() pada legacy.
     */
    public function getMonitoring(string $keyword)
    {
        return $this->normalizeRow(DB::connection('prod_new')->selectOne(
            "SELECT *
             FROM T_OP_REEFER
             WHERE NO_CONT = ?
               AND WAKTU IS NOT NULL
               AND WAKTU_END IS NULL
             ORDER BY ID DESC
             FETCH FIRST 1 ROWS ONLY",
            [strtoupper(trim($keyword))]
        ));
    }

    /**
     * Mengambil temperatur monitoring sebelumnya.
     * Mengambil temperatur monitoring dari prod_new.
     */
    public function getLastTemperature(string $keyword, ?int $activePlugId = null): ?object
    {
        if ($activePlugId === null) {
            return null;
        }

        $row = DB::connection('prod_new')
            ->table('T_OP_REEFER')
            ->select('TEMPERATURE_MONITOR', 'ID')
            ->whereNotNull('TEMPERATURE_MONITOR')
            ->where('NO_CONT', strtoupper(trim($keyword)))
            ->whereNull('WAKTU_END')
            ->where('FL_MONITOR', 'Y')
            ->where('ID', '>=', $activePlugId)
            ->orderByDesc('ID')
            ->first();

        return $this->normalizeRow($row);
    }

    /**
    * Validasi dan simpan record monitoring pada Oracle prod_new.
     */
    public function storeMonitoring(array $data, string $username): array
    {
        $noCont = strtoupper(trim($data['no_cont']));
        $wPlugin = $data['w_plugin'] ?? null;

        /*
         * Validasi bisnis dilakukan di prod_new.
         * Jika w_plugin kosong, mengikuti behavior legacy:
         * container dianggap sudah unplugin.
         */
        if ($wPlugin === null || trim((string) $wPlugin) === '') {
            throw new \InvalidArgumentException(
                "NO CONT : {$noCont} SUDAH UNPLUGIN."
            );
        }

        /*
         * Validasi container/SPK di prod_new.
         * Query join mengikuti implementasi legacy M_operation.
         */
        $spkCont = $this->normalizeRow(DB::connection('prod_new')->selectOne(
            "SELECT
                A.NO_CONT,
                A.ID AS ID_SPK_CONT,
                A.STATUS_CONT,
                B.NO_SPK
             FROM T_SPK_CONT A
             INNER JOIN T_SPK B ON A.ID = B.ID
             WHERE A.NO_CONT = ?
             ORDER BY A.ID DESC
             FETCH FIRST 1 ROWS ONLY",
            [$noCont]
        ));

        if (!$spkCont || strtoupper(trim((string) $spkCont->NO_CONT)) !== $noCont) {
            throw new \InvalidArgumentException(
                "NO CONT : {$noCont} NOT FOUND."
            );
        }

        if (empty($spkCont->NO_SPK)) {
            throw new \InvalidArgumentException(
                "Data SPK untuk NO CONT : {$noCont} tidak ditemukan."
            );
        }

        /*
         * Pastikan container masih dalam status plugin
         * berdasarkan data prod_new.
         */
        $activeReefer = DB::connection('prod_new')->selectOne(
            "SELECT ID, NO_CONT, WAKTU, WAKTU_END
             FROM T_OP_REEFER
             WHERE NO_CONT = ?
               AND WAKTU IS NOT NULL
               AND WAKTU_END IS NULL
             ORDER BY ID DESC
             FETCH FIRST 1 ROWS ONLY",
            [$noCont]
        );

        if (!$activeReefer) {
            throw new \InvalidArgumentException(
                "NO CONT : {$noCont} sudah unplugin atau tidak aktif monitoring."
            );
        }

        /*
         * Penulisan hanya ke Oracle prod_new.
         * Semua validasi sumber telah dilakukan di prod_new.
         */
        $connection = DB::connection('prod_new');

        try {
            $connection->beginTransaction();

            $insertData = [
                'NO_SPK'             => $spkCont->NO_SPK,
                'NO_CONT'            => $noCont,
                'WAKTU_MONITOR'      => now()->format('Y-m-d H:i:s'),
                'TEMPERATURE_MONITOR'=> $data['temperature'],
                'FL_MONITOR'         => 'Y',
                'NOTE'               => $data['note'] ?? null,
                'OPERATOR_MONITOR'   => $username,
                'WK_REKAM'           => now()->format('Y-m-d H:i:s'),
            ];

            /*
             * ID tidak disertakan.
             * Oracle harus mengisi ID melalui identity/trigger/default.
             */
            $connection->table('T_OP_REEFER')->insert($insertData);

            $connection->commit();

            return [
                'no_cont'             => $noCont,
                'no_spk'              => $spkCont->NO_SPK,
                'temperature_monitor' => $data['temperature'],
                'operator_monitor'    => $username,
                'waktu_monitor'       => now()->format('Y-m-d H:i:s'),
            ];
        } catch (Throwable $e) {
            if ($connection->transactionLevel() > 0) {
                $connection->rollBack();
            }

            throw $e;
        }
    }

    private function normalizeRows(array $rows): array
    {
        return array_map(
            fn ($row) => $this->normalizeRow($row),
            $rows
        );
    }

    private function normalizeRow(?object $row): ?object
    {
        if ($row === null) {
            return null;
        }

        $normalized = new \stdClass;
        foreach ((array) $row as $column => $value) {
            $normalized->{strtoupper($column)} = $value;
        }

        return $normalized;
    }
}
