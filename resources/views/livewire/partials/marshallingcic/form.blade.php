@php
    $jobActivitiesList = $job_activities ?? [];
    $equipmentsList = $equipments ?? [];
    $trucksList = $trucks ?? [];
    $operatorsList = $operators ?? [];
@endphp

<div class="mt-4 rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">

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

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        <div>
            <label class="text-xs font-semibold uppercase text-zinc-500">
                ID Job
            </label>

            <input
                type="text"
                readonly
                value="{{ $rows->ID_JOB_SLIP ?? '-' }}"
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
                value="{{ $rows->NO_CONT ?? '-' }}"
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
                value="{{ $rows->UKR_CONT ?? '-' }}"
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
                value="{{ $rows->JENIS ?? '-' }}"
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
                value="{{ ($rows->LOKASI_AWAL ?? '') !== '' ? $rows->LOKASI_AWAL . '0' . ($rows->TIER_AWAL ?? '') : '-' }}"
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
                value="{{ ($rows->LOKASI_AKHIR ?? '') !== '' ? $rows->LOKASI_AKHIR . '0' . ($rows->TIER_AKHIR ?? '') : '-' }}"
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
                value="{{ !empty($rows->RESPON) ? $rows->RESPON : 'NO RESPON' }}"
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
                value="{{ $rows->LNSW_NOAJU ?? '-' }}"
                class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-800"
            >
        </div>

    </div>

    <div class="mt-6 border-t border-zinc-200 pt-5 dark:border-zinc-700">
        <div class="mb-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">
                Informasi Aktivitas
            </p>

            <p class="mt-1 text-sm text-zinc-500">
                Data master tersedia dari legacy M_operation.
            </p>
        </div>

        <div class="space-y-4">

            {{-- Aktivitas 1 --}}
            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                <p class="mb-3 text-sm font-bold text-zinc-900 dark:text-white">
                    Aktivitas 1
                </p>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Job
                        </label>

                        <select
                            disabled
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">
                                Pilih Job
                            </option>

                            @foreach ($jobActivitiesList as $activity)
                                <option value="{{ $activity->ID ?? '' }}">
                                    {{ $activity->JNS_KEGIATAN ?? $activity->NAMA ?? $activity->NAME ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Alat
                        </label>

                        <select
                            disabled
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">
                                Pilih Alat
                            </option>

                            @foreach ($equipmentsList as $equipment)
                                <option value="{{ $equipment->ID ?? '' }}">
                                    {{ $equipment->NM_ALAT ?? $equipment->NO_ALAT ?? $equipment->CODE ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Operator
                        </label>

                        <select
                            disabled
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">
                                Pilih Operator
                            </option>

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
            <div class="rounded-2xl border border-zinc-200 p-4 dark:border-zinc-700">
                <p class="mb-3 text-sm font-bold text-zinc-900 dark:text-white">
                    Aktivitas 2
                </p>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Job
                        </label>

                        <select
                            disabled
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">
                                Pilih Job
                            </option>

                            @foreach ($jobActivitiesList as $activity)
                                <option value="{{ $activity->ID ?? '' }}">
                                    {{ $activity->JNS_KEGIATAN ?? $activity->NAMA ?? $activity->NAME ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Truck
                        </label>

                        <select
                            disabled
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">
                                Pilih Truck
                            </option>

                            @foreach ($trucksList as $truck)
                                <option value="{{ $truck->ID ?? '' }}">
                                    {{ $truck->NO_TRUCK ?? $truck->NO_FLAT ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Operator
                        </label>

                        <select
                            disabled
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">
                                Pilih Operator
                            </option>

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
                        <label class="text-xs font-semibold text-zinc-500">
                            Job
                        </label>

                        <select
                            disabled
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">
                                Pilih Job
                            </option>

                            @foreach ($jobActivitiesList as $activity)
                                <option value="{{ $activity->ID ?? '' }}">
                                    {{ $activity->JNS_KEGIATAN ?? $activity->NAMA ?? $activity->NAME ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Alat
                        </label>

                        <select
                            disabled
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">
                                Pilih Alat
                            </option>

                            @foreach ($equipmentsList as $equipment)
                                <option value="{{ $equipment->ID ?? '' }}">
                                    {{ $equipment->NM_ALAT ?? $equipment->NO_ALAT ?? $equipment->CODE ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-zinc-500">
                            Operator
                        </label>

                        <select
                            disabled
                            class="mt-1 w-full rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <option value="">
                                Pilih Operator
                            </option>

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
    </div>

    <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/20 dark:text-amber-300">
        Marshalling CIC saat ini dalam mode <strong>read-only</strong>. Tidak ada INSERT, UPDATE, atau DELETE.
    </div>

</div>