<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['1', 'SPPB PIB (BC 2.0)', 'IMP', 'RELEASE', 'N', 'BC20', 'BC', 'SPB'],
            ['2', 'SPPB PIB BC 2.3', 'IMP', 'RELEASE', 'H', 'BC23', 'BC', 'B23'],
            ['3', 'PERSETUJUAN PLP', 'IMP', 'RELEASE', 'H', 'PLP', 'BC', 'PLP'],
            ['4', 'SPPB BC 1.2', 'IMP', 'RELEASE', 'H', 'SPPBBC12', 'BC', 'B12'],
            ['5', 'BCF 2.6A', 'IMP', 'RELEASE', 'H', 'F26', 'BC', 'F26'],
            ['6', 'NPE', 'EXP', 'RELEASE', 'H', 'NPE', 'BC', 'NPE'],
            ['7', 'PKBE', 'EXP', null, 'H', null, 'BC', 'KBE'],
            ['8', 'PPB', 'EXP', null, 'H', null, 'BC', null],
            ['9', 'BCF 1.5 / Barang Tidak Dikuasai', 'IMP', 'RELEASE', 'H', 'BCF1.5', 'BC', 'F15'],
            ['10', 'EMPTY KONTAINER (IMPOR)', 'IMP', 'RELEASE', 'H', 'EMPTY', 'BC', 'NDI'],
            ['11', 'SPPBE', 'EXP', 'RELEASE', 'H', 'SPPBE11', 'BC', 'SBE'],
            ['12', 'SPPBE', 'EXP', 'RELEASE', 'H', 'SPPBE12', 'BC', 'SBE'],
            ['13', 'PIBK', 'IMP', 'RELEASE', 'H', 'PIBK', 'BC', 'PIK'],
            ['14', 'RETURNABLE PACKAGE (RP)', 'IMP', 'RELEASE', 'H', 'RPIM', 'BC', 'RP'],
            ['15', 'PENIMBUNAN', 'IMP', 'RELEASE', 'H', 'PKP', 'BC', 'PKP'],
            ['16', 'PERSETUJUAN SHORT SHIP', 'IMP', 'RELEASE', 'H', 'SHS', 'BC', 'SHS'],
            ['17', 'PART OF MITA', 'IMP', 'RELEASE', 'H', 'MITA', 'BC', 'PPM'],
            ['18', 'PART OF NON MITA', 'IMP', 'RELEASE', 'H', 'PMT', 'BC', 'PMT'],
            ['19', 'SPJM', 'IMP', null, 'H', 'SPJM', 'BC', 'SPJ'],
            ['20', 'DOKUMEN BC 1.1A / SP3B IMPOR', 'IMP', null, 'H', null, 'BC', 'SP3'],
            ['21', 'PENGELUARAN DENGAN PIB MANUAL (CUKAI)', 'IMP', null, 'H', 'PDPM', 'BC', 'PIM'],
            ['22', 'PAKET POS', 'IMP', null, 'H', 'POS', 'BC', 'POS'],
            ['23', 'PENGELUARAN BARANG UNTUK DIMUSNAHKAN', 'IMP', 'RELEASE', 'H', 'PBM', 'BC', 'PBM'],
            ['24', 'PENGELUARAN BARANG UNTUK BARANG BUKTI KE PENGADILAN', 'IMP', 'RELEASE', 'H', 'PBB', 'BC', 'PBB'],
            ['25', 'PENGELUARAN BARANG HIBAH', 'IMP', null, 'H', null, 'BC', 'PBH'],
            ['26', 'PENGELUARAN BARANG MILIK NEGARA', 'IMP', null, 'H', null, 'BC', null],
            ['27', 'PENGELUARAN BARANG PERS RELEASE', 'IMP', null, 'H', null, 'BC', 'PPR'],
            ['28', 'RE-EKSPOR (BC 1.2) BELUM AJU PIB', 'IMP', 'RELEASE', 'H', 'REBC1.2', 'BC', 'REX'],
            ['29', 'PENGELUARAN BARANG EKS PERGANTIAN KONTAINER', 'IMP', null, 'H', null, 'BC', 'GCT'],
            ['30', 'PENGELUARAN BARANG PENEGAHAN (SEBAGIAN)', 'IMP', 'RELEASE', 'H', 'SBS', 'BC', 'SBS'],
            ['31', 'NHI / PENGELUARAN BARANG PENEGAHAN (SELURUHNYA)', 'IMP', 'RELEASE', 'H', 'NHIPBP', 'BC', 'PBP'],
            ['32', 'EMPTY KONTAINER (EKSPOR)', 'EXP', null, 'H', null, 'BC', 'AGD'],
            ['33', 'Dokumen BC. 1.1 B/ SP3B Ekspor', 'EXP', null, 'H', null, 'BC', 'RPE'],
            ['34', 'SPPB BC.12 KPPT', 'EXP', null, 'H', null, 'BC', 'KPT'],
            ['35', 'ATA CARNET Impor', 'IMP', 'RELEASE', 'H', 'ATAI', 'BC', 'ATA'],
            ['36', 'CPD CARNET Impor', 'IMP', 'RELEASE', 'H', 'CPD', 'BC', 'CPD'],
            ['37', 'ATA CARNET Ekspor', 'EXP', 'RELEASE', 'H', null, 'BC', 'ATE'],
            ['38', 'CPD CARNET Ekspor', 'EXP', 'RELEASE', 'H', null, 'BC', 'CPE'],
            ['39', 'Dokumen Pemeriksaan Karantina', 'IMP', null, 'H', 'SPPMP', 'KRT', 'SOR'],
            ['40', 'PENGELUARAN KONTAINER EKS STRIPPING', 'IMP', null, 'H', null, 'BC', 'ECS'],
            ['41', 'BC 1.6', 'IMP', 'RELEASE', 'H', 'SPPBBC16', 'BC', 'B16'],
            ['42', 'Persetujuan Keluar Barang Kiriman', 'IMP', 'RELEASE', 'H', 'PKBK', 'BC', 'BKR'],
            ['43', 'SPPBMC', 'IMP', 'RELEASE', 'H', 'ATAI', 'BC', 'SMC'],
            ['44', 'Dokumen Pengeluaran Barang Impor dengan BC 1.1 Outward Manifes', 'EXP', null, 'H', 'ATAE', 'BC', 'B11'],
            ['45', 'NP P3BET', 'EXP', null, 'H', 'P3BET', 'BC', 'BET'],
            ['54', 'SP3K KONTAINER KOSONG', 'IMP', 'RELEASE', 'H', 'S3K', 'BC', 'S3K'],
            ['58', 'Penyerahan ke Kementrian/Lembaga Lain', 'IMP', 'RELEASE', 'H', 'PKL', 'BC', 'PKL'],
            ['80', 'SPPF[OLD]', 'IMP', null, 'H', 'F26', 'BC', 'F26'],
            ['81', 'NHI', 'IMP', null, 'H', 'NHII', 'BC', 'NHI'],
            ['82', 'NOTA DINAS', 'IMP', 'RELEASE', 'H', 'NODIN', 'BC', null],
            ['83', 'SPPMP', 'IMP', null, 'H', 'SPPMP', 'KRT', 'SQR'],
            ['84', 'SPJK', 'IMP', null, 'H', 'SPJK', 'BC', null],
            ['85', 'KT9 / KH12 / KID 5', 'IMP', null, 'H', null, 'KRT', null],
            ['86', 'RETURNABLE PACKAGE (RP) PELEPASAN', 'IMP', 'RELEASE', 'H', 'RPPS', 'BC', 'RP'],
            ['99', 'DOKUMEN PENGELUARAN LAINNYA', 'IMP', 'RELEASE', 'H', 'DLL', 'BC', 'DLL'],
        ];

        foreach ($data as [$id, $name, $direction, $processType, $autogateHold, $autogateDocType, $permitType, $npct1Code]) {
            DB::table('document_types')->updateOrInsert(
                ['id' => $id],
                [
                    'name' => $name,
                    'direction' => $direction,
                    'process_type' => $processType,
                    'autogate_hold' => $autogateHold,
                    'autogate_doc_type' => $autogateDocType,
                    'permit_type' => $permitType,
                    'npct1_code' => $npct1Code,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}