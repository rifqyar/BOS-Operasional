<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Throwable;

class MonitoringReeferServices
{
    /**
     * Mengambil data container yang masih plugin.
     * Seluruh pembacaan menggunakan database legacy prod.
     */
    public function getMonitoring(string $keyword)
    {
        return DB::connection('prod')->selectOne(
            "SELECT *
             FROM t_op_reefer
             WHERE WAKTU IS NOT NULL
               AND NO_CONT LIKE ?
               AND WAKTU_END IS NULL
             ORDER BY ID ASC
             LIMIT 1",
            ['%' . $keyword . '%']
        );
    }

    /**
     * Mengambil temperatur monitoring sebelumnya.
     * Seluruh pembacaan menggunakan database legacy prod.
     */
    public function getLastTemperature(string $keyword)
    {
        return DB::connection('prod')->selectOne(
            "SELECT TEMPERATURE_MONITOR
             FROM t_op_reefer
             WHERE WAKTU_MONITOR IS NOT NULL
               AND NO_CONT LIKE ?
               AND WAKTU_END IS NULL
               AND FL_MONITOR = 'Y'
             ORDER BY ID DESC
             LIMIT 1",
            ['%' . $keyword . '%']
        );
    }

    /**
     * Validasi container dan SPK dari database legacy,
     * lalu insert record monitoring ke Oracle prod_new.
     */
    public function storeMonitoring(array $data, string $username): array
    {
        $noCont = strtoupper(trim($data['no_cont']));
        $wPlugin = $data['w_plugin'] ?? null;

        /*
         * Validasi bisnis dilakukan di prod.
         * Jika w_plugin kosong, mengikuti behavior legacy:
         * container dianggap sudah unplugin.
         */
        if ($wPlugin === null || trim((string) $wPlugin) === '') {
            throw new \InvalidArgumentException(
                "NO CONT : {$noCont} SUDAH UNPLUGIN."
            );
        }

        /*
         * Validasi container/SPK di database legacy.
         * Query join mengikuti implementasi legacy M_operation.
         */
        $spkCont = DB::connection('prod')->selectOne(
            "SELECT A.*, B.*
             FROM t_spk_cont A
             INNER JOIN t_spk B ON A.ID = B.ID
             WHERE A.NO_CONT = ?
             ORDER BY A.ID DESC
             LIMIT 1",
            [$noCont]
        );

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
         * berdasarkan data legacy.
         */
        $activeReefer = DB::connection('prod')->selectOne(
            "SELECT ID, NO_CONT, WAKTU, WAKTU_END
             FROM t_op_reefer
             WHERE NO_CONT = ?
               AND WAKTU IS NOT NULL
               AND WAKTU_END IS NULL
             ORDER BY ID DESC
             LIMIT 1",
            [$noCont]
        );

        if (!$activeReefer) {
            throw new \InvalidArgumentException(
                "NO CONT : {$noCont} sudah unplugin atau tidak aktif monitoring."
            );
        }

        /*
         * Penulisan hanya ke Oracle prod_new.
         * Semua validasi sumber telah dilakukan di prod.
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
}