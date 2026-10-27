@php
    $row = $row ?? null;

    $jobActivitiesList = $jobActivities ?? $job_activities ?? [];
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

        {{-- Data utama --}}
        <input type="hidden" name="idJobSlip" value="{{ $idJobSlip }}">
        <input type="hidden" name="spk" value="460">
        <input type="hidden" name="nocont" value="{{ $noCont }}">
        <input type="hidden" name="ukrcont" value="{{ $ukrCont }}">
        <input type="hidden" name="lokaw" value="{{ $lokasiAwal }}">
        <input type="hidden" name="lokak" value="{{ $lokasiAkhir }}">
        <input type="hidden" name="job" value="{{ $jenisJob }}">
        <input type="hidden" name="respon" value="{{ $respon }}">

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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

            <div>
                <label class="text-xs font-semibold uppercase text-zinc-500">
                    No Container
                </label>
                <input
                    type="text"
                    readonly
                    value="{{ $noCont ?: '-' }}"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm font-bold uppercase dark:border-zinc-700 dark:bg-zinc-800"
                >
            </div>

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

        <div class="mt-6 border-t border-zinc-200 pt-5 dark:border-zinc-700">
            <p class="mb-4 text-xs font-semibold uppercase tracking-wide text-zinc-500">
                Informasi Aktivitas
            </p>

            {{-- Aktivitas 1 --}}
            <div class="mb-4 rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                <p class="mb-3 text-sm font-bold text-zinc-900 dark:text-white">
                    Aktivitas 1
                </p>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-semibold text-zinc-500">Job</label>
                        <select name="jenispekerjaan1"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">Pilih Job</option>
                            @foreach ($jobActivitiesList as $activity)
                                <option value="{{ $activity->ID ?? '' }}">
                                    {{ $activity->JNS_KEGIATAN ?? $activity->NAMA ?? $activity->NAME ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">Alat</label>
                        <select name="alat1"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">Pilih Alat</option>
                            @foreach ($equipmentsList as $equipment)
                                <option value="{{ $equipment->ID ?? '' }}">
                                    {{ $equipment->NM_ALAT ?? $equipment->NO_ALAT ?? $equipment->CODE ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">Operator</label>
                        <select name="operator1"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">Pilih Operator</option>
                            @foreach ($operatorsList as $operator)
                                <option value="{{ $operator->ID ?? '' }}">
                                    {{ $operator->NAMA ?? $operator->NAME ?? $operator->USERNAME ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Aktivitas 2 --}}
            <div class="mb-4 rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                <p class="mb-3 text-sm font-bold text-zinc-900 dark:text-white">
                    Aktivitas 2
                </p>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-semibold text-zinc-500">Job</label>
                        <select name="jenispekerjaan2"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">Pilih Job</option>
                            @foreach ($jobActivitiesList as $activity)
                                <option value="{{ $activity->ID ?? '' }}">
                                    {{ $activity->JNS_KEGIATAN ?? $activity->NAMA ?? $activity->NAME ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">Truck</label>
                        <select name="truck1"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">Pilih Truck</option>
                            @foreach ($trucksList as $truck)
                                <option value="{{ $truck->ID ?? '' }}">
                                    {{ $truck->NO_TRUCK ?? $truck->NO_FLAT ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">Operator</label>
                        <select name="operator2"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">Pilih Operator</option>
                            @foreach ($operatorsList as $operator)
                                <option value="{{ $operator->ID ?? '' }}">
                                    {{ $operator->NAMA ?? $operator->NAME ?? $operator->USERNAME ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Aktivitas 3 --}}
            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                <p class="mb-3 text-sm font-bold text-zinc-900 dark:text-white">
                    Aktivitas 3
                </p>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-semibold text-zinc-500">Job</label>
                        <select name="jenispekerjaan3"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">Pilih Job</option>
                            @foreach ($jobActivitiesList as $activity)
                                <option value="{{ $activity->ID ?? '' }}">
                                    {{ $activity->JNS_KEGIATAN ?? $activity->NAMA ?? $activity->NAME ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">Alat</label>
                        <select name="alat3"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">Pilih Alat</option>
                            @foreach ($equipmentsList as $equipment)
                                <option value="{{ $equipment->ID ?? '' }}">
                                    {{ $equipment->NM_ALAT ?? $equipment->NO_ALAT ?? $equipment->CODE ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">Operator</label>
                        <select name="operator3"
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <option value="">Pilih Operator</option>
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

        {{-- Catatan dan fumigasi --}}
        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="text-xs font-semibold text-zinc-500">Catatan</label>
                <textarea name="note" rows="3"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"></textarea>
            </div>

            <div>
                <label class="text-xs font-semibold text-zinc-500">Fumigasi</label>
                <select name="fumigasi"
                    class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <option value="">Tidak</option>
                    <option value="Y">Ya</option>
                </select>
            </div>
        </div>

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