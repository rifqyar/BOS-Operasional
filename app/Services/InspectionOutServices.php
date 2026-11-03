<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InspectionOutServices
{
    /**
     * Ambil gatepass untuk kegiatan Inspection Out.
     * Semua pembacaan menggunakan database legacy (prod).
     */
    public function getGatepass(string $noCont)
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_gatepass
             WHERE NO_CONT LIKE ?
               AND JNS_KEGIATAN = '3'",
            ['%' . $noCont . '%']
        );
    }

    /**
     * Ambil delivery yang belum Gate Out.
     */
    public function getDelivery(string $noCont)
    {
        return DB::connection('prod')->select(
            "SELECT *
             FROM t_op_delivery
             WHERE NO_CONT LIKE ?
               AND WK_GATEOUT IS NULL",
            ['%' . $noCont . '%']
        );
    }

    /**
     * Ambil referensi kondisi container.
     */
    public function getCondition()
    {
        return DB::connection('prod')->select(
            "SELECT ID, KONDISI
             FROM reff_kondisi"
        );
    }

    /**
     * Search data untuk ditampilkan pada form.
     */
    public function getInspectionData(string $noCont): array
    {
        $gatepass = $this->getGatepass($noCont);

        if (count($gatepass) === 0) {
            return $this->notFound($noCont);
        }

        $delivery = $this->getDelivery($noCont);

        if (count($delivery) === 0) {
            return $this->notFound($noCont);
        }

        /*
         * Prioritaskan exact match agar pencarian parsial
         * tidak mengambil container yang salah.
         */
        $exactRows = array_values(array_filter(
            $delivery,
            fn ($row) => strtoupper(trim((string) ($row->NO_CONT ?? '')))
                === strtoupper(trim($noCont))
        ));

        if (count($exactRows) > 1) {
            return [
                'status' => 0,
                'kode' => 1,
                'no_cont' => $noCont,
                'message' => 'Ditemukan lebih dari satu data container. Gunakan nomor container yang lengkap.',
            ];
        }

        $deliveryRow = count($exactRows) === 1
            ? $exactRows[0]
            : (count($delivery) === 1 ? $delivery[0] : null);

        if (!$deliveryRow) {
            return [
                'status' => 0,
                'kode' => 1,
                'no_cont' => $noCont,
                'message' => 'Pencarian menemukan beberapa container. Masukkan nomor container yang lebih lengkap.',
            ];
        }

        $readyForInspectionOut =
            $deliveryRow->WK_TRUCKIN !== null
            && $deliveryRow->WK_CHASSIS !== null
            && $deliveryRow->WK_INSPECT === null;

        if (!$readyForInspectionOut) {
            return $this->notFound($noCont);
        }

        return [
            'status' => 2,
            'no_cont' => $deliveryRow->NO_CONT ?? $noCont,
            'ukuran' => $deliveryRow->UKR_CONT ?? null,
            'no_seal' => $deliveryRow->NO_SEAL ?? null,
            'delivery' => $deliveryRow,
            'condition' => $this->getCondition(),
        ];
    }

    /**
     * Validasi ulang saat submit.
     *
     * Pembacaan dan validasi dilakukan pada prod (legacy).
     */
    public function validateInspectionOut(
        string $noCont,
        string $conditionId
    ): array {
        $gatepass = DB::connection('prod')->select(
            "SELECT *
             FROM t_gatepass
             WHERE NO_CONT = ?
               AND JNS_KEGIATAN = '3'",
            [$noCont]
        );

        if (count($gatepass) === 0) {
            return [
                'valid' => false,
                'message' => "Gatepass kegiatan Inspection Out untuk container {$noCont} tidak ditemukan.",
            ];
        }

        $delivery = DB::connection('prod')->select(
            "SELECT *
             FROM t_op_delivery
             WHERE NO_CONT = ?
               AND WK_GATEOUT IS NULL",
            [$noCont]
        );

        if (count($delivery) === 0) {
            return [
                'valid' => false,
                'message' => "Data delivery container {$noCont} tidak ditemukan atau sudah Gate Out.",
            ];
        }

        $deliveryRow = $delivery[0];

        if (
            $deliveryRow->WK_TRUCKIN === null
            || $deliveryRow->WK_CHASSIS === null
            || $deliveryRow->WK_INSPECT !== null
        ) {
            return [
                'valid' => false,
                'message' => "Container {$noCont} belum memenuhi syarat Inspection Out atau sudah diproses.",
            ];
        }

        /*
         * Kondisi harus berasal dari referensi legacy.
         */
        $condition = DB::connection('prod')
            ->table('reff_kondisi')
            ->where('ID', $conditionId)
            ->exists();

        if (!$condition) {
            return [
                'valid' => false,
                'message' => 'Kondisi container tidak valid.',
            ];
        }

        /*
         * Mengikuti legacy cek_tspkcontst2:
         * STATUS_CONT IN (450, 800, 850, 870)
         */
        $spkCont = DB::connection('prod')
            ->table('t_spk_cont')
            ->where('NO_CONT', $noCont)
            ->whereIn('STATUS_CONT', [450, 800, 850, 870])
            ->first();

        if (!$spkCont) {
            return [
                'valid' => false,
                'message' => "Status container {$noCont} tidak memenuhi syarat Inspection Out.",
            ];
        }

        /*
         * Legacy cek_tspk2 mengambil t_spk berdasarkan ID
         * dari t_spk_cont.
         */
        $spk = DB::connection('prod')
            ->table('t_spk')
            ->where('ID', $spkCont->ID)
            ->first();

        if (!$spk) {
            return [
                'valid' => false,
                'message' => "Data SPK untuk container {$noCont} tidak ditemukan.",
            ];
        }

        return [
            'valid' => true,
            'delivery' => $deliveryRow,
            'spk_cont' => $spkCont,
            'spk' => $spk,
        ];
    }

    private function notFound(string $noCont): array
    {
        return [
            'status' => 0,
            'kode' => 1,
            'no_cont' => $noCont,
            'message' => "WARNING ! NO CONT : {$noCont} NOT FOUND",
        ];
    }
}