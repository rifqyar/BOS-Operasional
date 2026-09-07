<div class="min-h-screen bg-slate-100 text-slate-800 dark:bg-slate-950 dark:text-slate-100">
    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">

        <div class="mb-5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('operation.pickup') }}"
                    wire:navigate
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                           bg-white text-slate-600 shadow-sm ring-1 ring-slate-200
                           hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300
                           dark:ring-slate-800"
                >
                    <flux:icon.arrow-left class="h-5 w-5" />
                </a>

                <div>
                    <div class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Menu Handheld
                    </div>

                    <h1 class="text-xl font-bold text-slate-900 dark:text-white">
                        Marshalling CIC
                    </h1>
                </div>
            </div>

            <div class="hidden rounded-xl bg-sky-100 px-3 py-2 text-xs font-semibold
                        text-sky-700 sm:block dark:bg-sky-950 dark:text-sky-300">
                Operations
            </div>
        </div>

        @if ($message)
            <div class="mb-5 rounded-xl border px-4 py-3 text-sm
                {{ $messageType === 'success'
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300'
                    : 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-300' }}">
                {{ $message }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3
                        text-sm text-rose-700 dark:border-rose-900 dark:bg-rose-950/40
                        dark:text-rose-300">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- SEARCH --}}
        <div class="mb-5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200
                    dark:bg-slate-900 dark:ring-slate-800">

            <div class="mb-4">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Search Container
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Cari container yang menunggu proses Marshalling CIC.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <div class="flex-1">
                    <label class="mb-1.5 block text-xs font-medium text-slate-600 dark:text-slate-300">
                        No Container
                    </label>

                    <input
                        type="text"
                        wire:model.live.debounce.400ms="searchCont"
                        placeholder="Cari No Container..."
                        autocomplete="off"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5
                               text-sm uppercase outline-none focus:border-sky-500
                               focus:ring-2 focus:ring-sky-500/20
                               dark:border-slate-700 dark:bg-slate-950"
                    >
                </div>

                <div class="flex items-end">
                    <button
                        type="button"
                        wire:click="$set('searchCont', '')"
                        class="min-h-11 rounded-xl bg-slate-100 px-5 py-2.5 text-sm
                               font-semibold text-slate-700 hover:bg-slate-200
                               dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                    >
                        Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        @if (!$showForm)
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200
                        dark:bg-slate-900 dark:ring-slate-800">

                <div class="border-b border-slate-200 px-4 py-4 dark:border-slate-800">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">
                        Daftar Marshalling CIC
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Pilih pekerjaan yang akan diproses.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider
                                      text-slate-500 dark:bg-slate-800/70">
                            <tr>
                                <th class="px-4 py-3 text-center">No</th>
                                <th class="px-4 py-3">ID Job</th>
                                <th class="px-4 py-3">No Container</th>
                                <th class="px-4 py-3">Ukuran</th>
                                <th class="px-4 py-3">Lokasi Awal</th>
                                <th class="px-4 py-3">Lokasi Akhir</th>
                                <th class="px-4 py-3">Job</th>
                                <th class="px-4 py-3">Respon</th>
                                <th class="px-4 py-3 text-center">Proses</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($jobs as $index => $job)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="px-4 py-3 text-center text-slate-500">
                                        {{ $jobs->firstItem() + $index }}
                                    </td>

                                    <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">
                                        {{ $job->no_job }}
                                    </td>

                                    <td class="px-4 py-3 font-semibold">
                                        {{ $job->spkContainer?->container?->no_cont }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $job->spkContainer?->container?->type?->size ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $job->locationFrom?->location_code ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $job->locationTo?->location_code ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $job->job_type }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $job->gatepass?->jenis_kegiatan ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <button
                                            type="button"
                                            wire:click="selectJob({{ $job->id }})"
                                            class="inline-flex items-center justify-center rounded-xl
                                                   bg-sky-600 px-4 py-2 text-xs font-bold text-white
                                                   hover:bg-sky-700"
                                        >
                                             MARSHALLING
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="9"
                                        class="px-4 py-10 text-center text-sm text-slate-500"
                                    >
                                        Tidak ada pekerjaan Marshalling CIC yang menunggu.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($jobs->hasPages())
                    <div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">
                        {{ $jobs->links() }}
                    </div>
                @endif
            </div>
        @endif

        {{-- FORM --}}
        @if ($showForm && $jobSlip)
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200
                        dark:bg-slate-900 dark:ring-slate-800">

                <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-4
                            sm:flex-row sm:items-center sm:justify-between
                            dark:border-slate-800">

                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">
                            Informasi Marshalling CIC
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Job Slip #{{ $jobSlip->id }}
                        </p>
                    </div>

                    <span class="w-fit rounded-full bg-sky-100 px-3 py-1 text-xs font-bold
                                 text-sky-700 dark:bg-sky-950 dark:text-sky-300">
                        {{ $jobSlip->job_type }}
                    </span>
                </div>

                <div class="space-y-6 p-4 sm:p-5">

                    <section>
                        <div class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">
                            Container
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                            <div>
                                <label class="mb-1.5 block text-xs font-medium">
                                    No Container
                                </label>

                                <input
                                    type="text"
                                    value="{{ $container?->no_cont }}"
                                    readonly
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50
                                           px-3 py-2.5 text-sm font-semibold
                                           dark:border-slate-700 dark:bg-slate-800"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium">
                                    Ukuran
                                </label>

                                <input
                                    type="text"
                                    value="{{ $container?->type?->size ?? '-' }}"
                                    readonly
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50
                                           px-3 py-2.5 text-sm
                                           dark:border-slate-700 dark:bg-slate-800"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium">
                                    Lokasi Awal
                                </label>

                                <input
                                    type="text"
                                    value="{{ $jobSlip->locationFrom?->location_code ?? '-' }}"
                                    readonly
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50
                                           px-3 py-2.5 text-sm
                                           dark:border-slate-700 dark:bg-slate-800"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium">
                                    Lokasi Akhir
                                </label>

                                <select
                                    wire:model="locationToId"
                                    class="w-full rounded-xl border border-slate-300 bg-white
                                           px-3 py-2.5 text-sm outline-none
                                           focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20
                                           dark:border-slate-700 dark:bg-slate-950"
                                >
                                    <option value="">Pilih Lokasi CIC</option>

                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">
                                            {{ $location->location_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium">
                                    Job
                                </label>

                                <input
                                    type="text"
                                    value="{{ $jobSlip->job_type }}"
                                    readonly
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50
                                           px-3 py-2.5 text-sm font-semibold
                                           dark:border-slate-700 dark:bg-slate-800"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium">
                                    Respon
                                </label>

                                <input
                                    type="text"
                                    value="{{ $respon ?: '-' }}"
                                    readonly
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50
                                           px-3 py-2.5 text-sm
                                           dark:border-slate-700 dark:bg-slate-800"
                                >
                            </div>

                        </div>
                    </section>

                    <section>
                        <div class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">
                            Catatan
                        </div>

                        <div class="space-y-4">

                            <textarea
                                wire:model="note"
                                rows="3"
                                maxlength="500"
                                placeholder="Catatan..."
                                class="w-full resize-none rounded-xl border border-slate-300
                                       bg-white px-3 py-2.5 text-sm outline-none
                                       focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20
                                       dark:border-slate-700 dark:bg-slate-950"
                            ></textarea>

                            <label class="flex cursor-pointer items-center gap-3 rounded-xl
                                          border border-slate-200 bg-slate-50 px-4 py-3
                                          dark:border-slate-700 dark:bg-slate-800">
                                <input
                                    type="checkbox"
                                    wire:model="fumigasi"
                                    class="h-4 w-4 rounded border-slate-300 text-sky-600"
                                >

                                <span>
                                    <span class="block text-sm font-semibold">
                                        Fumigasi
                                    </span>

                                    <span class="block text-xs text-slate-500">
                                        Tandai Y jika container fumigasi.
                                    </span>
                                </span>
                            </label>

                        </div>
                    </section>

                    <section>
                        <div class="mb-3">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-500">
                                Informasi Alat
                            </div>
                        </div>

                        <div class="space-y-4">

                            {{-- ACTIVITY 1 --}}
                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                                <div class="mb-4 flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg
                                                 bg-sky-100 text-xs font-bold text-sky-700
                                                 dark:bg-sky-950 dark:text-sky-300">
                                        1
                                    </span>

                                    <span class="text-sm font-bold">
                                        Aktivitas 1
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                                    <div>
                                        <label class="mb-1.5 block text-xs font-medium">
                                            Jenis Pekerjaan
                                        </label>

                                        <select
                                            wire:model.number="jenisPekerjaan1"
                                            class="w-full rounded-xl border border-slate-300 bg-white
                                                   px-3 py-2.5 text-sm dark:border-slate-700
                                                   dark:bg-slate-950"
                                        >
                                            <option value="0">Tidak Ada Aktivitas</option>
                                            <option value="3">LIFT ON STAGGER</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs font-medium">
                                            Alat
                                        </label>

                                        <select
                                            wire:model="alat1"
                                            @disabled($jenisPekerjaan1 === 0)
                                            class="w-full rounded-xl border border-slate-300 bg-white
                                                   px-3 py-2.5 text-sm disabled:bg-slate-100
                                                   dark:border-slate-700 dark:bg-slate-950
                                                   dark:disabled:bg-slate-800"
                                        >
                                            <option value="">Pilih Alat</option>

                                            @foreach ($equipments as $equipment)
                                                <option value="{{ $equipment->id }}">
                                                    {{ $equipment->code }} - {{ $equipment->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs font-medium">
                                            Operator
                                        </label>

                                        <select
                                            wire:model="operator1"
                                            @disabled($jenisPekerjaan1 === 0)
                                            class="w-full rounded-xl border border-slate-300 bg-white
                                                   px-3 py-2.5 text-sm disabled:bg-slate-100
                                                   dark:border-slate-700 dark:bg-slate-950
                                                   dark:disabled:bg-slate-800"
                                        >
                                            <option value="">Pilih Operator</option>

                                            @foreach ($operators as $operator)
                                                <option value="{{ $operator->id }}">
                                                    {{ $operator->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>

                            {{-- ACTIVITY 2 --}}
                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                                <div class="mb-4 flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg
                                                 bg-sky-100 text-xs font-bold text-sky-700
                                                 dark:bg-sky-950 dark:text-sky-300">
                                        2
                                    </span>

                                    <span class="text-sm font-bold">
                                        Aktivitas 2
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                                    <div>
                                        <label class="mb-1.5 block text-xs font-medium">
                                            Jenis Pekerjaan
                                        </label>

                                        <select
                                            wire:model.number="jenisPekerjaan2"
                                            class="w-full rounded-xl border border-slate-300 bg-white
                                                   px-3 py-2.5 text-sm dark:border-slate-700
                                                   dark:bg-slate-950"
                                        >
                                            <option value="0">Tidak Ada Kegiatan</option>
                                            <option value="6">Haulage</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs font-medium">
                                            Truck
                                        </label>

                                        <select
                                            wire:model="truck1"
                                            @disabled($jenisPekerjaan2 === 0)
                                            class="w-full rounded-xl border border-slate-300 bg-white
                                                   px-3 py-2.5 text-sm disabled:bg-slate-100
                                                   dark:border-slate-700 dark:bg-slate-950
                                                   dark:disabled:bg-slate-800"
                                        >
                                            <option value="">Pilih Truck</option>

                                            @foreach ($trucks as $truck)
                                                <option value="{{ $truck->id }}">
                                                    {{ $truck->no_truck ?: 'Truck #' . $truck->id }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs font-medium">
                                            Operator
                                        </label>

                                        <select
                                            wire:model="operator2"
                                            @disabled($jenisPekerjaan2 === 0)
                                            class="w-full rounded-xl border border-slate-300 bg-white
                                                   px-3 py-2.5 text-sm disabled:bg-slate-100
                                                   dark:border-slate-700 dark:bg-slate-950
                                                   dark:disabled:bg-slate-800"
                                        >
                                            <option value="">Pilih Operator</option>

                                            @foreach ($operators as $operator)
                                                <option value="{{ $operator->id }}">
                                                    {{ $operator->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>

                            {{-- ACTIVITY 3 --}}
                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                                <div class="mb-4 flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg
                                                 bg-sky-100 text-xs font-bold text-sky-700
                                                 dark:bg-sky-950 dark:text-sky-300">
                                        3
                                    </span>

                                    <span class="text-sm font-bold">
                                        Aktivitas 3
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                                    <div>
                                        <label class="mb-1.5 block text-xs font-medium">
                                            Jenis Pekerjaan
                                        </label>

                                        <select
                                            wire:model.number="jenisPekerjaan3"
                                            class="w-full rounded-xl border border-slate-300 bg-white
                                                   px-3 py-2.5 text-sm dark:border-slate-700
                                                   dark:bg-slate-950"
                                        >
                                            <option value="0">Tidak Ada Kegiatan</option>
                                            <option value="5">LIFT ON CHASSIS</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs font-medium">
                                            Alat
                                        </label>

                                        <select
                                            wire:model="alat3"
                                            @disabled($jenisPekerjaan3 === 0)
                                            class="w-full rounded-xl border border-slate-300 bg-white
                                                   px-3 py-2.5 text-sm disabled:bg-slate-100
                                                   dark:border-slate-700 dark:bg-slate-950
                                                   dark:disabled:bg-slate-800"
                                        >
                                            <option value="">Pilih Alat</option>

                                            @foreach ($equipments as $equipment)
                                                <option value="{{ $equipment->id }}">
                                                    {{ $equipment->code }} - {{ $equipment->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs font-medium">
                                            Operator
                                        </label>

                                        <select
                                            wire:model="operator3"
                                            @disabled($jenisPekerjaan3 === 0)
                                            class="w-full rounded-xl border border-slate-300 bg-white
                                                   px-3 py-2.5 text-sm disabled:bg-slate-100
                                                   dark:border-slate-700 dark:bg-slate-950
                                                   dark:disabled:bg-slate-800"
                                        >
                                            <option value="">Pilih Operator</option>

                                            @foreach ($operators as $operator)
                                                <option value="{{ $operator->id }}">
                                                    {{ $operator->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </section>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5
                                sm:flex-row sm:justify-end dark:border-slate-800">

                        <button
                            type="button"
                            wire:click="cancelProcess"
                            wire:loading.attr="disabled"
                            class="min-h-12 rounded-xl bg-slate-100 px-5 py-3 text-sm
                                   font-bold text-slate-700 hover:bg-slate-200
                                   dark:bg-slate-800 dark:text-slate-200"
                        >
                            Kembali
                        </button>

                        <button
                            type="button"
                            wire:click="save"
                            wire:confirm="Yakin ingin menyimpan Marshalling CIC untuk {{ $container?->no_cont }}?"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="min-h-12 rounded-xl bg-sky-600 px-6 py-3 text-sm
                                   font-bold text-white hover:bg-sky-700
                                   disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="save">
                                Simpan Marshalling CIC
                            </span>

                            <span wire:loading wire:target="save">
                                Menyimpan...
                            </span>
                        </button>

                    </div>

                </div>
            </div>
        @endif

    </div>
</div>