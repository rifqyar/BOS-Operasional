<form data-store-form>
    @csrf

    <input
        type="hidden"
        name="idJobSlip"
        value="{{ $row->ID_JOB_SLIP }}"
    >

    <input
        type="hidden"
        name="spk"
        value="450"
    >

    <input
        type="hidden"
        name="nocont"
        value="{{ $row->NO_CONT }}"
    >

    <input
        type="hidden"
        name="ukrcont"
        value="{{ $row->UKR_CONT }}"
    >

    <input
        type="hidden"
        name="lokaw"
        value="{{ $row->LOKASI_AWAL }}0{{ $row->TIER_AWAL }}"
    >

    <div class="mb-4">
        <label for="lokak">Lokasi Akhir</label>

        <input
            type="text"
            id="lokak"
            name="lokak"
            value="{{ $row->LOKASI_AKHIR }}0{{ $row->TIER_AWAL }}"
            class="form-control"
            required
        >
    </div>

    <input
        type="hidden"
        name="job"
        value="{{ $row->JENIS }}"
    >

    <input
        type="hidden"
        name="respon"
        value="{{ $row->RESPON ?? '' }}"
    >

    <div class="mb-4">
        <label for="jenispekerjaan1">Jenis Pekerjaan 1</label>

        <select
            id="jenispekerjaan1"
            name="jenispekerjaan1"
            class="form-control"
            data-activity="1"
        >
            <option value="0">Tidak Ada Kegiatan</option>

            @foreach ($job_activities as $activity)
                <option value="{{ $activity->ID }}">
                    {{ $activity->NM_JOB_ACTIVITY ?? $activity->NAMA ?? $activity->ID }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="alat1">Alat 1</label>

        <select
            id="alat1"
            name="alat1"
            class="form-control"
            data-dependent="1"
            disabled
        >
            <option value="">Pilih alat</option>

            @foreach ($equipments as $equipment)
                <option value="{{ $equipment->ID }}">
                    {{ $equipment->NM_ALAT ?? $equipment->NAMA_ALAT ?? $equipment->ID }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="operator1">Operator 1</label>

        <select
            id="operator1"
            name="operator1"
            class="form-control"
            data-dependent="1"
            disabled
        >
            <option value="">Pilih operator</option>

            @foreach ($operators as $operator)
                <option value="{{ $operator->ID }}">
                    {{ $operator->USERLOGIN ?? $operator->NAMA ?? $operator->ID }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="jenispekerjaan2">Jenis Pekerjaan 2</label>

        <select
            id="jenispekerjaan2"
            name="jenispekerjaan2"
            class="form-control"
            data-activity="2"
        >
            <option value="0">Tidak Ada Kegiatan</option>

            @foreach ($job_activities as $activity)
                <option value="{{ $activity->ID }}">
                    {{ $activity->NM_JOB_ACTIVITY ?? $activity->NAMA ?? $activity->ID }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="truck1">Truk</label>

        <select
            id="truck1"
            name="truck1"
            class="form-control"
            data-dependent="2"
            disabled
        >
            <option value="">Pilih truk</option>

            @foreach ($trucks as $truck)
                <option value="{{ $truck->ID }}">
                    {{ $truck->NO_TRUCK }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="operator2">Operator 2</label>

        <select
            id="operator2"
            name="operator2"
            class="form-control"
            data-dependent="2"
            disabled
        >
            <option value="">Pilih operator</option>

            @foreach ($operators as $operator)
                <option value="{{ $operator->ID }}">
                    {{ $operator->USERLOGIN ?? $operator->NAMA ?? $operator->ID }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="jenispekerjaan3">Jenis Pekerjaan 3</label>

        <select
            id="jenispekerjaan3"
            name="jenispekerjaan3"
            class="form-control"
            data-activity="3"
        >
            <option value="0">Tidak Ada Kegiatan</option>

            @foreach ($job_activities as $activity)
                <option value="{{ $activity->ID }}">
                    {{ $activity->NM_JOB_ACTIVITY ?? $activity->NAMA ?? $activity->ID }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="alat3">Alat 3</label>

        <select
            id="alat3"
            name="alat3"
            class="form-control"
            data-dependent="3"
            disabled
        >
            <option value="">Pilih alat</option>

            @foreach ($equipments as $equipment)
                <option value="{{ $equipment->ID }}">
                    {{ $equipment->NM_ALAT ?? $equipment->NAMA_ALAT ?? $equipment->ID }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="operator3">Operator 3</label>

        <select
            id="operator3"
            name="operator3"
            class="form-control"
            data-dependent="3"
            disabled
        >
            <option value="">Pilih operator</option>

            @foreach ($operators as $operator)
                <option value="{{ $operator->ID }}">
                    {{ $operator->USERLOGIN ?? $operator->NAMA ?? $operator->ID }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="note">Catatan</label>

        <textarea
            id="note"
            name="note"
            class="form-control"
            maxlength="500"
        ></textarea>
    </div>

    <button
        type="submit"
        data-store-button
        class="btn btn-primary"
    >
        Simpan Marshalling Yard
    </button>
</form>