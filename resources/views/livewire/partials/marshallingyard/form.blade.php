@php
    $jobActivitiesList = $job_activities ?? ($references['job_activities'] ?? []);
    $equipmentsList = $equipments ?? ($references['equipments'] ?? []);
    $operatorsList = $operators ?? ($references['operators'] ?? []);
    $trucksList = $trucks ?? ($references['trucks'] ?? []);

    $lokasiAwal = trim(($row->LOKASI_AWAL ?? '') . '0' . ($row->TIER_AWAL ?? ''));
    $lokasiAkhir = trim(($row->LOKASI_AKHIR ?? '') . '0' . ($row->TIER_AWAL ?? ''));

    $jobSlipId = $row->ID_JOB_SLIP ?? '';
    $noCont = $row->NO_CONT ?? '';
    $ukuranCont = $row->UKR_CONT ?? '';
    $jenisJob = $row->JENIS ?? '';
    $respon = $row->RESPON ?? '';

    $selectClass = 'h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white disabled:cursor-not-allowed disabled:bg-slate-100 disabled:opacity-60 dark:disabled:bg-white/5';
    $readonlyClass = 'h-11 w-full min-w-0 rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white';
    $labelClass = 'text-xs font-semibold uppercase text-slate-500 dark:text-slate-400';
@endphp

<form data-store-form class="space-y-6 p-4 sm:p-6">
    @csrf

    {{-- HIDDEN FIELDS --}}
    <input type="hidden" name="idJobSlip" value="{{ $jobSlipId }}">
    <input type="hidden" name="nocont" value="{{ $noCont }}">
    <input type="hidden" name="ukrcont" value="{{ $ukuranCont }}">
    <input type="hidden" name="lokaw" value="{{ $lokasiAwal }}">
    <input type="hidden" name="job" value="{{ $jenisJob }}">
    <input type="hidden" name="respon" value="{{ $respon }}">

    {{-- SECTION 1: INFORMASI KONTAINER --}}
    <section class="space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-3 dark:border-white/10">
            <span class="flex size-8 items-center justify-center rounded-md bg-sky-100 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">
                <flux:icon.archive-box class="size-4" />
            </span>

            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                Informasi Kontainer
            </h3>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">No. Container</span>
                <input
                    type="text"
                    value="{{ $noCont }}"
                    readonly
                    class="{{ $readonlyClass }}"
                >
            </label>

            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Ukuran Container</span>
                <input
                    type="text"
                    value="{{ $ukuranCont }}"
                    readonly
                    class="{{ $readonlyClass }}"
                >
            </label>

            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Jenis Job</span>
                <input
                    type="text"
                    value="{{ $jenisJob }}"
                    readonly
                    class="{{ $readonlyClass }}"
                >
            </label>

            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Lokasi Awal</span>
                <input
                    type="text"
                    value="{{ $lokasiAwal }}"
                    readonly
                    class="{{ $readonlyClass }}"
                >
            </label>

            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Lokasi Akhir</span>
                <input
                    type="text"
                    id="lokak"
                    name="lokak"
                    value="{{ $lokasiAkhir }}"
                    required
                    placeholder="Masukkan lokasi akhir"
                    class="{{ $selectClass }}"
                >
            </label>
        </div>
    </section>

    {{-- SECTION 2: KEGIATAN 1 --}}
    <section class="space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-3 dark:border-white/10">
            <span class="flex size-8 items-center justify-center rounded-md bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                <flux:icon.wrench-screwdriver class="size-4" />
            </span>

            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                Kegiatan 1
            </h3>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Jenis Pekerjaan</span>

                <select
                    id="jenispekerjaan1"
                    name="jenispekerjaan1"
                    data-activity="1"
                    class="{{ $selectClass }}"
                >
                    <option value="0">Tidak Ada Kegiatan</option>

                    @foreach ($jobActivitiesList as $activity)
                        @php
                            $activityId = $activity->ID
                                ?? $activity->ID_JOB_ACTIVITY
                                ?? $activity->ID_JOB
                                ?? '';

                            $activityName = $activity->JENIS_PEKERJAAN
                                ?? $activity->NM_JOB_ACTIVITY
                                ?? $activity->NAMA
                                ?? $activityId;
                        @endphp

                        @if ($activityId !== '')
                            <option value="{{ $activityId }}">
                                {{ $activityName }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </label>

            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Alat</span>

                <select
                    id="alat1"
                    name="alat1"
                    data-dependent="1"
                    disabled
                    class="{{ $selectClass }}"
                >
                    <option value="">Pilih alat</option>

                    @foreach ($equipmentsList as $equipment)
                        @php
                            $equipmentId = $equipment->ID
                                ?? $equipment->ID_ALAT
                                ?? $equipment->CODE
                                ?? $equipment->NO_ALAT
                                ?? '';

                            $equipmentName = $equipment->NM_ALAT
                                ?? $equipment->NAMA_ALAT
                                ?? $equipment->NO_ALAT
                                ?? $equipment->CODE
                                ?? $equipmentId;
                        @endphp

                        @if ($equipmentId !== '')
                            <option value="{{ $equipmentId }}">
                                {{ $equipmentName }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </label>

            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Operator</span>

                <select
                    id="operator1"
                    name="operator1"
                    data-dependent="1"
                    disabled
                    class="{{ $selectClass }}"
                >
                    <option value="">Pilih operator</option>

                    @foreach ($operatorsList as $operator)
                        @php
                            $operatorId = $operator->ID
                                ?? $operator->ID_USER
                                ?? $operator->USERLOGIN
                                ?? $operator->USER_NAME
                                ?? '';

                            $operatorName = $operator->USERLOGIN
                                ?? $operator->USER_NAME
                                ?? $operator->NAMA
                                ?? $operatorId;
                        @endphp

                        @if ($operatorId !== '')
                            <option value="{{ $operatorId }}">
                                {{ $operatorName }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </label>
        </div>
    </section>

    {{-- SECTION 3: KEGIATAN 2 --}}
    <section class="space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-3 dark:border-white/10">
            <span class="flex size-8 items-center justify-center rounded-md bg-violet-100 text-violet-700 dark:bg-violet-400/10 dark:text-violet-300">
                <flux:icon.truck class="size-4" />
            </span>

            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                Kegiatan 2
            </h3>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Jenis Pekerjaan</span>

                <select
                    id="jenispekerjaan2"
                    name="jenispekerjaan2"
                    data-activity="2"
                    class="{{ $selectClass }}"
                >
                    <option value="0">Tidak Ada Kegiatan</option>

                    @foreach ($jobActivitiesList as $activity)
                        @php
                            $activityId = $activity->ID
                                ?? $activity->ID_JOB_ACTIVITY
                                ?? $activity->ID_JOB
                                ?? '';

                            $activityName = $activity->JENIS_PEKERJAAN
                                ?? $activity->NM_JOB_ACTIVITY
                                ?? $activity->NAMA
                                ?? $activityId;
                        @endphp

                        @if ($activityId !== '')
                            <option value="{{ $activityId }}">
                                {{ $activityName }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </label>

            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Truk</span>

                <select
                    id="truck1"
                    name="truck1"
                    data-dependent="2"
                    disabled
                    class="{{ $selectClass }}"
                >
                    <option value="">Pilih truk</option>

                    @foreach ($trucksList as $truck)
                        @php
                            $truckId = $truck->NO_TRUCK
                                ?? $truck->ID
                                ?? $truck->ID_TRUCK
                                ?? '';

                            $truckName = $truck->NO_TRUCK
                                ?? $truck->NO_FLAT
                                ?? $truck->ID
                                ?? $truckId;
                        @endphp

                        @if ($truckId !== '')
                            <option value="{{ $truckId }}">
                                {{ $truckName }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </label>

            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Operator</span>

                <select
                    id="operator2"
                    name="operator2"
                    data-dependent="2"
                    disabled
                    class="{{ $selectClass }}"
                >
                    <option value="">Pilih operator</option>

                    @foreach ($operatorsList as $operator)
                        @php
                            $operatorId = $operator->ID
                                ?? $operator->ID_USER
                                ?? $operator->USERLOGIN
                                ?? $operator->USER_NAME
                                ?? '';

                            $operatorName = $operator->USERLOGIN
                                ?? $operator->USER_NAME
                                ?? $operator->NAMA
                                ?? $operatorId;
                        @endphp

                        @if ($operatorId !== '')
                            <option value="{{ $operatorId }}">
                                {{ $operatorName }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </label>
        </div>
    </section>

    {{-- SECTION 4: KEGIATAN 3 --}}
    <section class="space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-3 dark:border-white/10">
            <span class="flex size-8 items-center justify-center rounded-md bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300">
                <flux:icon.clipboard-document-check class="size-4" />
            </span>

            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                Kegiatan 3
            </h3>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Jenis Pekerjaan</span>

                <select
                    id="jenispekerjaan3"
                    name="jenispekerjaan3"
                    data-activity="3"
                    class="{{ $selectClass }}"
                >
                    <option value="0">Tidak Ada Kegiatan</option>

                    @foreach ($jobActivitiesList as $activity)
                        @php
                            $activityId = $activity->ID
                                ?? $activity->ID_JOB_ACTIVITY
                                ?? $activity->ID_JOB
                                ?? '';

                            $activityName = $activity->JENIS_PEKERJAAN
                                ?? $activity->NM_JOB_ACTIVITY
                                ?? $activity->NAMA
                                ?? $activityId;
                        @endphp

                        @if ($activityId !== '')
                            <option value="{{ $activityId }}">
                                {{ $activityName }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </label>

            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Alat</span>

                <select
                    id="alat3"
                    name="alat3"
                    data-dependent="3"
                    disabled
                    class="{{ $selectClass }}"
                >
                    <option value="">Pilih alat</option>

                    @foreach ($equipmentsList as $equipment)
                        @php
                            $equipmentId = $equipment->ID
                                ?? $equipment->ID_ALAT
                                ?? $equipment->CODE
                                ?? $equipment->NO_ALAT
                                ?? '';

                            $equipmentName = $equipment->NM_ALAT
                                ?? $equipment->NAMA_ALAT
                                ?? $equipment->NO_ALAT
                                ?? $equipment->CODE
                                ?? $equipmentId;
                        @endphp

                        @if ($equipmentId !== '')
                            <option value="{{ $equipmentId }}">
                                {{ $equipmentName }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </label>

            <label class="grid min-w-0 gap-1">
                <span class="{{ $labelClass }}">Operator</span>

                <select
                    id="operator3"
                    name="operator3"
                    data-dependent="3"
                    disabled
                    class="{{ $selectClass }}"
                >
                    <option value="">Pilih operator</option>

                    @foreach ($operatorsList as $operator)
                        @php
                            $operatorId = $operator->ID
                                ?? $operator->ID_USER
                                ?? $operator->USERLOGIN
                                ?? $operator->USER_NAME
                                ?? '';

                            $operatorName = $operator->USERLOGIN
                                ?? $operator->USER_NAME
                                ?? $operator->NAMA
                                ?? $operatorId;
                        @endphp

                        @if ($operatorId !== '')
                            <option value="{{ $operatorId }}">
                                {{ $operatorName }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </label>
        </div>
    </section>

    {{-- SECTION 5: CATATAN --}}
    <section class="space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-3 dark:border-white/10">
            <span class="flex size-8 items-center justify-center rounded-md bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300">
                <flux:icon.chat-bubble-left-right class="size-4" />
            </span>

            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                Catatan
            </h3>
        </div>

        <label class="grid min-w-0 gap-1">
            <span class="{{ $labelClass }}">Catatan Tambahan</span>

            <textarea
                id="note"
                name="note"
                rows="3"
                maxlength="500"
                placeholder="Masukkan catatan jika diperlukan"
                class="w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white"
            ></textarea>
        </label>
    </section>

    {{-- SUBMIT BUTTON --}}
    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end dark:border-white/10">
        <button
            type="submit"
            data-store-button
            class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-md bg-emerald-600 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto dark:focus:ring-offset-slate-950"
        >
            <flux:icon.check class="size-4" />
            <span>Simpan Marshalling Yard</span>
        </button>
    </div>
</form>

{{-- DROPDOWN DEPENDENCY --}}
<script>
    (() => {
        const form = document.querySelector('[data-store-form]');

        if (!form || form.dataset.dependenciesInitialized === 'true') {
            return;
        }

        form.dataset.dependenciesInitialized = 'true';

        const activityGroups = [
            {
                activity: 'jenispekerjaan1',
                dependent: ['alat1', 'operator1'],
            },
            {
                activity: 'jenispekerjaan2',
                dependent: ['truck1', 'operator2'],
            },
            {
                activity: 'jenispekerjaan3',
                dependent: ['alat3', 'operator3'],
            },
        ];

        function updateDependentFields(activitySelect, dependentIds) {
            const hasActivity =
                activitySelect.value !== '' &&
                activitySelect.value !== '0';

            dependentIds.forEach((id) => {
                const field = form.querySelector(`#${id}`);

                if (!field) {
                    return;
                }

                field.disabled = !hasActivity;

                if (!hasActivity) {
                    field.value = '';
                }
            });
        }

        activityGroups.forEach((group) => {
            const activitySelect = form.querySelector(`#${group.activity}`);

            if (!activitySelect) {
                return;
            }

            activitySelect.addEventListener('change', () => {
                updateDependentFields(
                    activitySelect,
                    group.dependent
                );
            });

            updateDependentFields(
                activitySelect,
                group.dependent
            );
        });
    })();
</script>