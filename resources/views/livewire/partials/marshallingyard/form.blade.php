@php
    $job_activities = $job_activities ?? [];
    $equipments = $equipments ?? [];
    $operators = $operators ?? [];
    $trucks = $trucks ?? [];
    $locations = $locations ?? [];

    $lokasiAwal = trim(
        (string) ($row->LOKASI_AWAL ?? '')
    );

    $tierAwal = trim(
        (string) ($row->TIER_AWAL ?? '')
    );

    $lokasiAkhir = trim(
        (string) ($row->LOKASI_AKHIR ?? '')
    );

    $tierAkhir = trim(
        (string) ($row->TIER_AKHIR ?? '')
    );

    $displayLokasiAwal =
        $lokasiAwal !== ''
            ? $lokasiAwal . ($tierAwal !== '' ? '0' . $tierAwal : '')
            : '-';

    $displayLokasiAkhir =
        $lokasiAkhir !== ''
            ? $lokasiAkhir . ($tierAkhir !== '' ? '0' . $tierAkhir : '')
            : '-';
@endphp

<div
    data-detail-card
    class="overflow-hidden rounded-2xl bg-white shadow-sm
           ring-1 ring-slate-200 dark:bg-slate-900
           dark:ring-slate-800"
>
    {{-- HEADER --}}
    <div
        class="flex flex-col gap-3 border-b border-slate-200
               px-4 py-4 sm:flex-row sm:items-center
               sm:justify-between sm:px-5
               dark:border-slate-800"
    >
        <div>
            <div
                class="text-[11px] font-medium uppercase
                       tracking-wider text-slate-500"
            >
                Informasi Marshalling Yard
            </div>

            <h2
                class="mt-1 text-lg font-bold
                       text-slate-900 dark:text-white"
            >
                Job Slip #{{ $row->ID_JOB_SLIP ?? '-' }}
            </h2>
        </div>

        <span
            class="w-fit rounded-full bg-sky-100 px-3 py-1.5
                   text-xs font-bold text-sky-700
                   dark:bg-sky-950 dark:text-sky-300"
        >
            {{ $row->JENIS ?? '-' }}
        </span>
    </div>

    <form
        data-store-form
        class="space-y-6 p-4 sm:p-5"
    >
        {{-- HIDDEN --}}
        <input
            type="hidden"
            name="idJobSlip"
            value="{{ $row->ID_JOB_SLIP ?? '' }}"
        >

        <input
            type="hidden"
            name="nocont"
            value="{{ $row->NO_CONT ?? '' }}"
        >

        <input
            type="hidden"
            name="ukrcont"
            value="{{ $row->UKR_CONT ?? '' }}"
        >

        <input
            type="hidden"
            name="job"
            value="{{ $row->JENIS ?? '' }}"
        >

        {{-- CONTAINER --}}
        <section>
            <div class="mb-3">
                <h3
                    class="text-xs font-bold uppercase
                           tracking-wider text-slate-500"
                >
                    Container
                </h3>
            </div>

            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2"
            >
                <div>
                    <label
                        class="mb-1.5 block text-xs font-medium"
                    >
                        No Container
                    </label>

                    <input
                        type="text"
                        value="{{ $row->NO_CONT ?? '-' }}"
                        readonly
                        class="min-h-12 w-full rounded-xl border
                               border-slate-200 bg-slate-50 px-3
                               text-sm font-bold
                               dark:border-slate-700
                               dark:bg-slate-800"
                    >
                </div>

                <div>
                    <label
                        class="mb-1.5 block text-xs font-medium"
                    >
                        Ukuran
                    </label>

                    <input
                        type="text"
                        value="{{ $row->UKR_CONT ?? '-' }}"
                        readonly
                        class="min-h-12 w-full rounded-xl border
                               border-slate-200 bg-slate-50 px-3
                               text-sm
                               dark:border-slate-700
                               dark:bg-slate-800"
                    >
                </div>

                <div>
                    <label
                        class="mb-1.5 block text-xs font-medium"
                    >
                        Lokasi Awal
                    </label>

                    <input
                        type="text"
                        value="{{ $displayLokasiAwal }}"
                        readonly
                        class="min-h-12 w-full rounded-xl border
                               border-slate-200 bg-slate-50 px-3
                               text-sm
                               dark:border-slate-700
                               dark:bg-slate-800"
                    >
                </div>

                <div>
                    <label
                        class="mb-1.5 block text-xs font-medium"
                    >
                        Lokasi Akhir
                    </label>

                <select
                    name="lokak"
                    required
                    class="min-h-12 w-full rounded-xl border
                        border-slate-300 bg-white px-3 text-sm outline-none
                        focus:border-sky-500
                        focus:ring-2 focus:ring-sky-500/20
                        dark:border-slate-700
                        dark:bg-slate-950"
                >
                    <option value="{{ $row->LOKASI_AKHIR ?? '' }}" selected>
                        {{ $displayLokasiAkhir }}
                    </option>
                </select>
                </div>

                <div>
                    <label
                        class="mb-1.5 block text-xs font-medium"
                    >
                        Job
                    </label>

                    <input
                        type="text"
                        value="{{ $row->JENIS ?? '-' }}"
                        readonly
                        class="min-h-12 w-full rounded-xl border
                               border-slate-200 bg-slate-50 px-3
                               text-sm font-semibold
                               dark:border-slate-700
                               dark:bg-slate-800"
                    >
                </div>

                <div>
                    <label
                        class="mb-1.5 block text-xs font-medium"
                    >
                        Respon
                    </label>

                    <input
                        type="text"
                        name="respon"
                        value="{{ $row->RESPON ?? '' }}"
                        readonly
                        class="min-h-12 w-full rounded-xl border
                               border-slate-200 bg-slate-50 px-3
                               text-sm
                               dark:border-slate-700
                               dark:bg-slate-800"
                    >
                </div>

                @if (!empty($row->LNSW_NOAJU))
                    <div>
                        <label
                            class="mb-1.5 block text-xs font-medium"
                        >
                            LNSW No Aju
                        </label>

                        <input
                            type="text"
                            value="{{ $row->LNSW_NOAJU }}"
                            readonly
                            class="min-h-12 w-full rounded-xl border
                                   border-slate-200 bg-slate-50 px-3
                                   text-sm
                                   dark:border-slate-700
                                   dark:bg-slate-800"
                        >
                    </div>
                @endif
            </div>
        </section>

        {{-- CATATAN --}}
        <section>
            <div class="mb-3">
                <h3
                    class="text-xs font-bold uppercase
                           tracking-wider text-slate-500"
                >
                    Catatan
                </h3>
            </div>

            <div class="space-y-4">
                <textarea
                    name="note"
                    rows="3"
                    maxlength="500"
                    placeholder="Catatan..."
                    class="w-full resize-none rounded-xl border
                           border-slate-300 bg-white px-3 py-3
                           text-sm outline-none
                           focus:border-sky-500
                           focus:ring-2 focus:ring-sky-500/20
                           dark:border-slate-700
                           dark:bg-slate-950"
                ></textarea>

                <label
                    class="flex min-h-14 cursor-pointer items-center
                           gap-3 rounded-xl border border-slate-200
                           bg-slate-50 px-4 py-3
                           dark:border-slate-700
                           dark:bg-slate-800"
                >
                    <input
                        type="checkbox"
                        name="fumigasi"
                        value="Y"
                        class="h-5 w-5 rounded border-slate-300
                               text-sky-600"
                    >

                    <span>
                        <span
                            class="block text-sm font-semibold"
                        >
                            Fumigasi
                        </span>

                        <span
                            class="block text-xs text-slate-500"
                        >
                            Tandai jika container fumigasi.
                        </span>
                    </span>
                </label>
            </div>
        </section>

        {{-- INFORMASI ALAT --}}
        <section>
            <div class="mb-3">
                <h3
                    class="text-xs font-bold uppercase
                           tracking-wider text-slate-500"
                >
                    Informasi Alat
                </h3>
            </div>

            <div class="space-y-4">

                {{-- AKTIVITAS 1 --}}
                <div
                    class="rounded-xl border border-slate-200 p-4
                           dark:border-slate-700"
                >
                    <div class="mb-4 flex items-center gap-2">
                        <span
                            class="flex h-8 w-8 shrink-0
                                   items-center justify-center
                                   rounded-lg bg-sky-100
                                   text-xs font-bold text-sky-700
                                   dark:bg-sky-950
                                   dark:text-sky-300"
                        >
                            1
                        </span>

                        <span class="text-sm font-bold">
                            Aktivitas 1
                        </span>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-4
                               sm:grid-cols-3"
                    >
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium"
                            >
                                Jenis Pekerjaan
                            </label>

                            <select
                                name="jenisPekerjaan1"
                                data-activity="1"
                                class="min-h-12 w-full rounded-xl
                                       border border-slate-300
                                       bg-white px-3 text-sm
                                       dark:border-slate-700
                                       dark:bg-slate-950"
                            >
                                <option value="0">
                                    Tidak Ada Kegiatan
                                </option>

                                <option value="7">
                                    LIFT OFF STAGGER - YARD
                                </option>

                                <option value="9">
                                    LIFT ON DELIVERY
                                </option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium"
                            >
                                Alat
                            </label>

                            <select
                                name="alat1"
                                data-dependent="1"
                                disabled
                                class="min-h-12 w-full rounded-xl
                                       border border-slate-300
                                       bg-white px-3 text-sm
                                       disabled:bg-slate-100
                                       dark:border-slate-700
                                       dark:bg-slate-950"
                            >
                                <option value="">
                                    Pilih Alat
                                </option>

                                @foreach ($equipments as $equipment)
                                    <option
                                        value="{{ $equipment->ID ?? $equipment->id ?? '' }}"
                                    >
                                        {{ $equipment->CODE ?? $equipment->code ?? '' }}
                                        -
                                        {{ $equipment->NAME ?? $equipment->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium"
                            >
                                Operator
                            </label>

                            <select
                                name="operator1"
                                data-dependent="1"
                                disabled
                                class="min-h-12 w-full rounded-xl
                                       border border-slate-300
                                       bg-white px-3 text-sm
                                       disabled:bg-slate-100
                                       dark:border-slate-700
                                       dark:bg-slate-950"
                            >
                                <option value="">
                                    Pilih Operator
                                </option>

                                @foreach ($operators as $operator)
                                    <option
                                        value="{{ $operator->ID ?? $operator->id ?? '' }}"
                                    >
                                        {{ $operator->NAME ?? $operator->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- AKTIVITAS 2 --}}
                <div
                    class="rounded-xl border border-slate-200 p-4
                           dark:border-slate-700"
                >
                    <div class="mb-4 flex items-center gap-2">
                        <span
                            class="flex h-8 w-8 shrink-0
                                   items-center justify-center
                                   rounded-lg bg-sky-100
                                   text-xs font-bold text-sky-700
                                   dark:bg-sky-950
                                   dark:text-sky-300"
                        >
                            2
                        </span>

                        <span class="text-sm font-bold">
                            Aktivitas 2
                        </span>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-4
                               sm:grid-cols-3"
                    >
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium"
                            >
                                Jenis Pekerjaan
                            </label>

                            <select
                                name="jenisPekerjaan2"
                                data-activity="2"
                                class="min-h-12 w-full rounded-xl
                                       border border-slate-300
                                       bg-white px-3 text-sm
                                       dark:border-slate-700
                                       dark:bg-slate-950"
                            >
                                <option value="0">
                                    Tidak Ada Kegiatan
                                </option>

                                <option value="8">
                                    HAULAGE - YARD
                                </option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium"
                            >
                                Truck
                            </label>

                            <select
                                name="truck1"
                                data-dependent="2"
                                disabled
                                class="min-h-12 w-full rounded-xl
                                       border border-slate-300
                                       bg-white px-3 text-sm
                                       disabled:bg-slate-100
                                       dark:border-slate-700
                                       dark:bg-slate-950"
                            >
                                <option value="">
                                    Pilih Truck
                                </option>

                                @foreach ($trucks as $truck)
                                    <option
                                        value="{{ $truck->ID ?? $truck->id ?? '' }}"
                                    >
                                        {{ $truck->NO_TRUCK ?? $truck->no_truck ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium"
                            >
                                Operator
                            </label>

                            <select
                                name="operator2"
                                data-dependent="2"
                                disabled
                                class="min-h-12 w-full rounded-xl
                                       border border-slate-300
                                       bg-white px-3 text-sm
                                       disabled:bg-slate-100
                                       dark:border-slate-700
                                       dark:bg-slate-950"
                            >
                                <option value="">
                                    Pilih Operator
                                </option>

                                @foreach ($operators as $operator)
                                    <option
                                        value="{{ $operator->ID ?? $operator->id ?? '' }}"
                                    >
                                        {{ $operator->NAME ?? $operator->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- AKTIVITAS 3 --}}
                <div
                    class="rounded-xl border border-slate-200 p-4
                           dark:border-slate-700"
                >
                    <div class="mb-4 flex items-center gap-2">
                        <span
                            class="flex h-8 w-8 shrink-0
                                   items-center justify-center
                                   rounded-lg bg-sky-100
                                   text-xs font-bold text-sky-700
                                   dark:bg-sky-950
                                   dark:text-sky-300"
                        >
                            3
                        </span>

                        <span class="text-sm font-bold">
                            Aktivitas 3
                        </span>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-4
                               sm:grid-cols-3"
                    >
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium"
                            >
                                Jenis Pekerjaan
                            </label>

                            <select
                                name="jenisPekerjaan3"
                                data-activity="3"
                                class="min-h-12 w-full rounded-xl
                                       border border-slate-300
                                       bg-white px-3 text-sm
                                       dark:border-slate-700
                                       dark:bg-slate-950"
                            >
                                <option value="0">
                                    Tidak Ada Kegiatan
                                </option>

                                <option value="10">
                                    LIFT OF HAULAGE - YARD
                                </option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium"
                            >
                                Alat
                            </label>

                            <select
                                name="alat3"
                                data-dependent="3"
                                disabled
                                class="min-h-12 w-full rounded-xl
                                       border border-slate-300
                                       bg-white px-3 text-sm
                                       disabled:bg-slate-100
                                       dark:border-slate-700
                                       dark:bg-slate-950"
                            >
                                <option value="">
                                    Pilih Alat
                                </option>

                                @foreach ($equipments as $equipment)
                                    <option
                                        value="{{ $equipment->ID ?? $equipment->id ?? '' }}"
                                    >
                                        {{ $equipment->CODE ?? $equipment->code ?? '' }}
                                        -
                                        {{ $equipment->NAME ?? $equipment->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium"
                            >
                                Operator
                            </label>

                            <select
                                name="operator3"
                                data-dependent="3"
                                disabled
                                class="min-h-12 w-full rounded-xl
                                       border border-slate-300
                                       bg-white px-3 text-sm
                                       disabled:bg-slate-100
                                       dark:border-slate-700
                                       dark:bg-slate-950"
                            >
                                <option value="">
                                    Pilih Operator
                                </option>

                                @foreach ($operators as $operator)
                                    <option
                                        value="{{ $operator->ID ?? $operator->id ?? '' }}"
                                    >
                                        {{ $operator->NAME ?? $operator->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- ACTION --}}
        <div
            class="flex flex-col-reverse gap-3 border-t
                   border-slate-200 pt-5 sm:flex-row
                   sm:justify-end dark:border-slate-800"
        >
            <button
                type="button"
                data-close-detail
                class="min-h-12 w-full rounded-xl bg-slate-100
                       px-5 py-3 text-sm font-bold
                       text-slate-700 transition
                       hover:bg-slate-200 sm:w-auto
                       dark:bg-slate-800 dark:text-slate-200
                       dark:hover:bg-slate-700"
            >
                Kembali
            </button>

            <button
                type="submit"
                data-store-button
                class="min-h-12 w-full rounded-xl bg-sky-600
                       px-6 py-3 text-sm font-bold text-white
                       transition hover:bg-sky-700
                       disabled:cursor-not-allowed
                       disabled:opacity-60 sm:w-auto"
            >
                Simpan Marshalling Yard
            </button>
        </div>
    </form>
</div>