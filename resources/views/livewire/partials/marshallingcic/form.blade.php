@php
    $row = $row ?? null;

    $equipmentsList = $equipments ?? [];
    $trucksList = $trucks ?? [];
    $operatorsList = $operators ?? [];

    $idJobSlip = $row->ID_JOB_SLIP ?? '';
    $noCont = $row->NO_CONT ?? '';
    $ukrCont = $row->UKR_CONT ?? '';
    $jenisJob = $row->JENIS ?? '';
    $lokasiAwal = $row->LOKASI_AWAL ?? '';
    $tierAwal = $row->TIER_AWAL ?? '';
    $lokasiAkhir = $row->LOKASI_AKHIR ?? '';
    $tierAkhir = $row->TIER_AKHIR ?? '';
    $respon = $row->RESPON ?? '';
    $noAju = $row->LNSW_NOAJU ?? '';
@endphp

<div
    class="mt-4 rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
    data-marshallingcic-form
>
    {{-- HEADER --}}
    <div class="mb-5 flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                Detail Job
            </p>

            <h3 class="mt-1 text-lg font-bold text-zinc-900 dark:text-white">
                Marshalling CIC
            </h3>
        </div>

        <button
            type="button"
            data-close-detail
            class="rounded-xl border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-600 dark:text-zinc-200"
        >
            Tutup
        </button>
    </div>

    <form data-cic-form>
        @csrf

        {{-- ==========================================
             DATA UTAMA
        =========================================== --}}
        <input type="hidden" name="idJobSlip" value="{{ $idJobSlip }}">
        <input type="hidden" name="spk" value="460">
        <input type="hidden" name="nocont" value="{{ $noCont }}">
        <input type="hidden" name="ukrcont" value="{{ $ukrCont }}">
        <input type="hidden" name="lokaw" value="{{ $lokasiAwal }}">
        <input type="hidden" name="lokak" value="{{ $lokasiAkhir }}">
        <input type="hidden" name="job" value="{{ $jenisJob }}">
        <input type="hidden" name="respon" value="{{ $respon }}">

        {{-- ==========================================
             DETAIL MARSHALLING CIC
        =========================================== --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

            {{-- ID JOB --}}
            <div>
                <label class="text-xs font-semibold uppercase text-zinc-500">
                    ID Job
                </label>

                <input
                    type="text"
                    readonly
                    value="{{ $idJobSlip ?: '-' }}"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                >
            </div>

            {{-- NOMOR CONTAINER --}}
            <div>
                <label class="text-xs font-semibold uppercase text-zinc-500">
                    No Kontainer
                </label>

                <input
                    type="text"
                    readonly
                    value="{{ $noCont ?: '-' }}"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm font-bold uppercase dark:border-zinc-700 dark:bg-zinc-800"
                >
            </div>

            {{-- UKURAN --}}
            <div>
                <label class="text-xs font-semibold uppercase text-zinc-500">
                    Ukuran
                </label>

                <input
                    type="text"
                    readonly
                    value="{{ $ukrCont ?: '-' }}"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                >
            </div>

            {{-- JOB UTAMA --}}
            <div>
                <label class="text-xs font-semibold uppercase text-zinc-500">
                    Job
                </label>

                <input
                    type="text"
                    readonly
                    value="{{ $jenisJob ?: '-' }}"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                >
            </div>

            {{-- LOKASI AWAL --}}
            <div>
                <label class="text-xs font-semibold uppercase text-zinc-500">
                    Lokasi Awal
                </label>

                <input
                    type="text"
                    readonly
                    value="{{ $lokasiAwal !== '' ? $lokasiAwal . '0' . $tierAwal : '-' }}"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                >
            </div>

            {{-- LOKASI AKHIR --}}
            <div>
                <label class="text-xs font-semibold uppercase text-zinc-500">
                    Lokasi Akhir
                </label>

                <input
                    type="text"
                    readonly
                    value="{{ $lokasiAkhir !== '' ? $lokasiAkhir . '0' . $tierAkhir : '-' }}"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                >
            </div>

            {{-- RESPON --}}
            <div>
                <label class="text-xs font-semibold uppercase text-zinc-500">
                    Respon
                </label>

                <input
                    type="text"
                    readonly
                    value="{{ $respon !== '' ? $respon : 'NO RESPON' }}"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                >
            </div>

            {{-- LNSW NO AJU --}}
            <div>
                <label class="text-xs font-semibold uppercase text-zinc-500">
                    LNSW No Aju
                </label>

                <input
                    type="text"
                    readonly
                    value="{{ $noAju ?: '-' }}"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                >
            </div>

        </div>

        {{-- ==========================================
             INFORMASI ALAT
        =========================================== --}}
        <div class="mt-6 border-t border-zinc-200 pt-5 dark:border-zinc-700">

            <p class="mb-4 text-xs font-semibold uppercase tracking-wide text-zinc-500">
                Informasi Alat
            </p>

            {{-- ======================================
                 INFORMASI ALAT 1
                 LEGACY:
                 3 = LIFT ON STAGGER
                 0 = Tidak Ada Aktivitas
            ======================================= --}}
            <div class="mb-4 rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">

                <p class="mb-4 text-sm font-bold text-zinc-900 dark:text-white">
                    Informasi Alat 1
                </p>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">

                    {{-- JENIS PEKERJAAN 1 --}}
                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Jenis Pekerjaan
                        </label>

                        <select
                            name="jenispekerjaan1"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">---Pilih Jenis Pekerjaan---</option>

                            <option value="3">
                                LIFT ON STAGGER
                            </option>

                            <option value="0">
                                Tidak Ada Aktivitas
                            </option>
                        </select>
                    </div>

                    {{-- ALAT 1 --}}
                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Alat
                        </label>

                        <select
                            name="alat1"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">---Pilih Alat---</option>

                            @foreach ($equipmentsList as $equipment)
                                <option value="{{ $equipment->ID ?? '' }}">
                                    {{ $equipment->NM_ALAT ?? $equipment->NO_ALAT ?? $equipment->CODE ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- OPERATOR 1 --}}
                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Operator
                        </label>

                        <select
                            name="operator1"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">---Pilih Operator---</option>

                            @foreach ($operatorsList as $operator)
                                <option value="{{ $operator->ID ?? '' }}">
                                    {{ $operator->NAMA ?? $operator->NAME ?? $operator->USERNAME ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            {{-- ======================================
                 INFORMASI ALAT 2
                 LEGACY:
                 0 = Tidak Ada Kegiatan
                 6 = Haulage
            ======================================= --}}
            <div class="mb-4 rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">

                <p class="mb-4 text-sm font-bold text-zinc-900 dark:text-white">
                    Informasi Alat 2
                </p>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">

                    {{-- JENIS PEKERJAAN 2 --}}
                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Jenis Pekerjaan
                        </label>

                        <select
                            name="jenispekerjaan2"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">---Pilih Jenis Pekerjaan---</option>

                            <option value="0">
                                Tidak Ada Kegiatan
                            </option>

                            <option value="6">
                                Haulage
                            </option>
                        </select>
                    </div>

                    {{-- TRUCK --}}
                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Truck
                        </label>

                        <select
                            name="truck1"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">---Pilih Truck---</option>

                            @foreach ($trucksList as $truck)
                                <option value="{{ $truck->ID ?? '' }}">
                                    {{ $truck->NO_TRUCK ?? $truck->NO_FLAT ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- OPERATOR 2 --}}
                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Operator
                        </label>

                        <select
                            name="operator2"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">---Pilih Operator---</option>

                            @foreach ($operatorsList as $operator)
                                <option value="{{ $operator->ID ?? '' }}">
                                    {{ $operator->NAMA ?? $operator->NAME ?? $operator->USERNAME ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            {{-- ======================================
                 INFORMASI ALAT 3
                 LEGACY:
                 0 = Tidak Ada Kegiatan
                 5 = LIFT ON CHASSIS
            ======================================= --}}
            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">

                <p class="mb-4 text-sm font-bold text-zinc-900 dark:text-white">
                    Informasi Alat 3
                </p>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">

                    {{-- JENIS PEKERJAAN 3 --}}
                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Jenis Pekerjaan
                        </label>

                        <select
                            name="jenispekerjaan3"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">---Pilih Jenis Pekerjaan---</option>

                            <option value="0">
                                Tidak Ada Kegiatan
                            </option>

                            <option value="5">
                                LIFT ON CHASSIS
                            </option>
                        </select>
                    </div>

                    {{-- ALAT 3 --}}
                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Alat
                        </label>

                        <select
                            name="alat3"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">---Pilih Alat---</option>

                            @foreach ($equipmentsList as $equipment)
                                <option value="{{ $equipment->ID ?? '' }}">
                                    {{ $equipment->NM_ALAT ?? $equipment->NO_ALAT ?? $equipment->CODE ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- OPERATOR 3 --}}
                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Operator
                        </label>

                        <select
                            name="operator3"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">---Pilih Operator---</option>

                            @foreach ($operatorsList as $operator)
                                <option value="{{ $operator->ID ?? '' }}">
                                    {{ $operator->NAMA ?? $operator->NAME ?? $operator->USERNAME ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

        </div>

        {{-- ==========================================
             CATATAN DAN FUMIGASI
        =========================================== --}}
        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">

            {{-- CATATAN --}}
            <div>
                <label class="text-xs font-semibold text-zinc-500">
                    Note
                </label>

                <textarea
                    name="note"
                    rows="3"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                ></textarea>
            </div>

            {{-- FUMIGASI --}}
            <div>
                <label class="text-xs font-semibold text-zinc-500">
                    Fumigasi
                </label>

                <select
                    name="fumigasi"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                >
                    <option value="">Tidak</option>
                    <option value="Y">Ya</option>
                </select>
            </div>

        </div>

        {{-- ==========================================
             TOMBOL PROSES
        =========================================== --}}
        <div class="mt-5 flex justify-end">
            <button
                type="submit"
                data-save-button
                class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
                PROSES
            </button>
        </div>

    </form>
</div>