@php
    $jobActivitiesList = $job_activities ?? ($references['job_activities'] ?? []);
    $equipmentsList = $equipments ?? ($references['equipments'] ?? []);
    $operatorsList = $operators ?? ($references['operators'] ?? []);
    $trucksList = $trucks ?? ($references['trucks'] ?? []);
    $containerConditionsList = $container_conditions ?? ($references['container_conditions'] ?? []);

    $isDg = (($rows->FL_DG ?? '') === 'Y') || !empty($rows->IMO);
    $selectedFlat = $rows->ID_FLAT ?? '';
    $selectedUkuran = $rows->UKR_CONT ?? '';
    $selectedTipe = strtoupper(trim($rows->TIPE_CONT ?? ''));
    $lokasiVal = trim($rows->LOKASI ?? '');
@endphp

<form data-send-form class="p-4 sm:p-6 space-y-6">
    @csrf

    {{-- HIDDEN FIELDS --}}
    <input type="hidden" id="id_request" name="id_request" value="{{ $rows->ID_REQUEST ?? '' }}">
    <input type="hidden" id="nocont" name="nocont" value="{{ $rows->NO_CONT ?? '' }}">
    <input type="hidden" id="idJobSlip" name="idJobSlip" value="{{ $rows->ID_JOB_SLIP ?? ($rows->ID ?? '') }}">
    <input type="hidden" id="idbehandlein" name="idbehandlein" value="{{ $rows->ID_BEHANDLE_IN ?? '' }}">

    {{-- SECTION 1: DATA KONTAINER --}}
    <div class="space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-3 dark:border-white/10">
            <span class="flex size-7 items-center justify-center rounded-md bg-sky-100 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">
                <flux:icon.archive-box class="size-4" />
            </span>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                Informasi Kontainer
            </h3>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {{-- NO CONT --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">NO CONT</span>
                <input type="text" id="No_cont" name="nomerkon" value="{{ $rows->NO_CONT ?? '' }}" required readonly
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white">
            </label>

            {{-- NO SPK --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">NO SPK</span>
                <input type="text" id="nospk" name="nospk" value="{{ $rows->NO_SPK ?? '' }}" required readonly
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white">
            </label>

            {{-- ISO CODE --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">ISO CODE</span>
                <input type="text" id="Iso_code" name="isocode" maxlength="4" required
                    value="{{ $rows->ISO_CODE ?? ($rows->ISO ?? '') }}"
                    placeholder="Contoh: 22G1"
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold uppercase text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white">
            </label>

            {{-- KONDISI SEAL --}}
            <div class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">KONDISI SEAL</span>
                <div class="flex h-11 items-center gap-4 rounded-md border border-slate-200 bg-white px-3 dark:border-white/10 dark:bg-slate-900">
                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-200">
                        <input type="radio" name="optradio" value="ada" checked
                            class="size-4 text-sky-600 focus:ring-sky-500">
                        <span>ADA</span>
                    </label>
                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-200">
                        <input type="radio" name="optradio" value="tidak ada"
                            class="size-4 text-sky-600 focus:ring-sky-500">
                        <span>TIDAK ADA</span>
                    </label>
                </div>
            </div>

            {{-- NO SEAL --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">NO SEAL</span>
                <input type="text" id="No_seal" name="noseal" required
                    value="{{ $rows->NO_SEAL ?? '' }}"
                    placeholder="Masukkan No Seal"
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:bg-slate-100 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:disabled:bg-white/5">
            </label>

            {{-- KONDISI KONTAINER --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">KONDISI KONTAINER</span>
                <select id="kond" name="kondisi" required
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white">
                    <option value="">-- Pilih Kondisi Kontainer --</option>
                    @foreach ($containerConditionsList as $row)
                        <option value="{{ $row->ID }}">{{ $row->KONDISI }}</option>
                    @endforeach
                </select>
            </label>

            {{-- TID --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">TID / NO TRUCK</span>
                <select id="trucknya" name="trucknya" required
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white">
                    @if (!empty($selectedFlat))
                        <option value="{{ $selectedFlat }}">{{ $selectedFlat }}</option>
                    @else
                        <option value="">-- Pilih Truk --</option>
                    @endif
                    @foreach ($trucksList as $rownya)
                        @if ($rownya->NO_TRUCK !== $selectedFlat)
                            <option value="{{ $rownya->NO_TRUCK }}">{{ $rownya->NO_TRUCK }}</option>
                        @endif
                    @endforeach
                </select>
            </label>

            {{-- LOKASI --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">LOKASI</span>
                <input type="text" id="nolok" name="nolok" value="{{ $lokasiVal }}" required
                    placeholder="Contoh: A1-01-01"
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white">
            </label>

            {{-- STATUS KONTAINER --}}
            <div class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">STATUS KONTAINER</span>
                <div class="flex h-11 items-center gap-4 rounded-md border border-slate-200 bg-white px-3 dark:border-white/10 dark:bg-slate-900">
                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-200">
                        <input type="radio" name="optradiostatus" value='{ "key1": "FL", "key2": "F"}' checked
                            class="size-4 text-sky-600 focus:ring-sky-500">
                        <span>FULL</span>
                    </label>
                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-200">
                        <input type="radio" name="optradiostatus" value='{ "key1": "M", "key2": "E"}'
                            class="size-4 text-sky-600 focus:ring-sky-500">
                        <span>EMPTY</span>
                    </label>
                </div>
            </div>

            {{-- UKURAN --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">UKURAN</span>
                <select id="ukuran" name="ukuran" required
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white">
                    <option value="20" @selected($selectedUkuran == '20')>20 Feet</option>
                    <option value="40" @selected($selectedUkuran == '40')>40 Feet</option>
                    <option value="45" @selected($selectedUkuran == '45')>45 Feet</option>
                </select>
            </label>

            {{-- TYPE CONTAINER --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">TYPE CONTAINER</span>
                <select id="tipe" name="tipe" required
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white">
                    @foreach (['DRY', 'HQ', 'OVD', 'TNK', 'OT', 'RFR'] as $typeOption)
                        <option value="{{ $typeOption }}" @selected($selectedTipe === $typeOption)>{{ $typeOption }}</option>
                    @endforeach
                </select>
            </label>

            {{-- LABEL --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">LABEL HAZARD</span>
                <select id="test" name="test"
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white">
                    <option value="" @selected(!$isDg)>NON DG</option>
                    <option value="DG" @selected($isDg)>DG</option>
                    <option value="DGNL">DG NON LABEL</option>
                </select>
            </label>
        </div>
    </div>

    {{-- SECTION 2: INFORMASI ALAT --}}
    <div class="space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-200 pb-3 dark:border-white/10">
            <span class="flex size-7 items-center justify-center rounded-md bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                <flux:icon.wrench-screwdriver class="size-4" />
            </span>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                Informasi Alat &amp; Operasional
            </h3>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {{-- JENIS PEKERJAAN --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Jenis Pekerjaan</span>
                <select id="jenisPekerjaan" name="jenisPekerjaan"
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white">
                    <option value="0">Tidak Ada Kegiatan</option>
                    @foreach ($jobActivitiesList as $activity)
                        <option value="{{ $activity->ID }}" @selected($activity->ID == 1)>
                            {{ $activity->JENIS_PEKERJAAN }}
                        </option>
                    @endforeach
                </select>
            </label>

            {{-- ALAT --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Alat</span>
                <select id="alat" name="alat"
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white">
                    <option value="">--- Pilih Alat ---</option>
                    @foreach ($equipmentsList as $equipment)
                        <option value="{{ $equipment->ID }}">
                            {{ $equipment->NM_ALAT }} {{ !empty($equipment->KEPEMILIKAN) ? '('.$equipment->KEPEMILIKAN.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </label>

            {{-- OPERATOR --}}
            <label class="grid min-w-0 gap-1">
                <span class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Operator</span>
                <input list="operator-list" id="operator" name="operator" autocomplete="off"
                    placeholder="Pilih atau cari operator..."
                    class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white">
                <datalist id="operator-list">
                    @foreach ($operatorsList as $opr)
                        <option value="{{ $opr->NAMA }}">{{ $opr->NAMA }} ({{ $opr->USER_NAME }})</option>
                    @endforeach
                </datalist>
            </label>
        </div>
    </div>

    {{-- BUTTON SUBMIT --}}
    <div class="flex items-center justify-end pt-3">
        <button type="submit"
            class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-md bg-emerald-600 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white disabled:cursor-not-allowed disabled:opacity-70 sm:w-auto dark:focus:ring-offset-slate-950">
            <flux:icon.check class="size-4" />
            <span>Simpan</span>
        </button>
    </div>
</form>
