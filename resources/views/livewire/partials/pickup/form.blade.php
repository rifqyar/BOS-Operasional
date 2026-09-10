<form data-send-form
    class="grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[minmax(0,1.15fr)_minmax(5rem,0.45fr)_minmax(0,1.55fr)_auto] lg:items-end">
    <input type="hidden" name="no_spk" value="{{ $rows->no_spk }}">
    <input type="hidden" name="no_container" value="{{ $rows->no_container }}">

    <label class="grid min-w-0 gap-1">
        <span class="text-xs font-semibold uppercase text-slate-500 sm:hidden dark:text-slate-400">No Kontainer</span>
        <input type="text" value="{{ $rows->no_container }}" readonly
            class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white">
    </label>

    <label class="grid min-w-0 gap-1">
        <span class="text-xs font-semibold uppercase text-slate-500 sm:hidden dark:text-slate-400">Ukuran</span>
        <input type="text" value="{{ $rows->ukuran }}" readonly
            class="h-11 w-full min-w-0 rounded-md border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-800 dark:border-white/10 dark:bg-white/5 dark:text-white">
    </label>

    <label class="grid min-w-0 gap-1 sm:col-span-2 lg:col-span-1">
        <span class="text-xs font-semibold uppercase text-slate-500 sm:hidden dark:text-slate-400">Nomer Truck</span>
        <select name="no_truck"
            class="h-11 w-full min-w-0 max-w-full rounded-md border border-slate-200 bg-white px-3 pr-9 text-sm font-semibold text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-slate-900 dark:text-white">
            @foreach ($trucks as $truck)
                <option value="{{ $truck->NO_TRUCK }}">
                    {{ $truck->NO_TRUCK }} - {{ $truck->NO_PLAT }} - {{ $truck->NM_PEMILIK }}
                </option>
            @endforeach
        </select>
    </label>

    <button type="submit"
        class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-md bg-emerald-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white disabled:cursor-not-allowed disabled:opacity-70 sm:col-span-2 lg:col-span-1 lg:w-auto dark:focus:ring-offset-slate-950">
        Send
    </button>
</form>
