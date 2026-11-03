(($) => {
    if (!$) {
        return;
    }

    const getPanel = () =>
        $('[data-panel][data-panel-name="plugreefer"]').first();

    const hasPanel = () =>
        getPanel().length > 0;

    const getNoContInput = () =>
        $('#panel-no-container', getPanel());

    const getSearchForm = () =>
        $('[data-search-form]', getPanel());

    const getSearchButton = () =>
        $('[data-search-button]', getPanel());

    const getResultWrapper = () =>
        $('[data-result]', getPanel());

    const getTableContainer = () =>
        $('[data-table]', getPanel());

    const getDetailContainer = () =>
        $('[data-detail]', getPanel());

    const getSearchUrl = () =>
        getPanel().data('searchUrl');

    const getDetailUrl = () =>
        getPanel().data('detailUrl');

    const getStoreUrl = () =>
        getPanel().data('storeUrl');

    const getCsrfToken = () =>
        getPanel().attr('data-csrf-token');


    /*
    |--------------------------------------------------------------------------
    | ALERT
    |--------------------------------------------------------------------------
    */

    const showAlert = (
        icon,
        title,
        text
    ) => {

        if (
            typeof window.showAlert ===
            'function'
        ) {
            window.showAlert(
                icon,
                title,
                text
            );

            return;
        }

        if (window.Swal) {

            Swal.fire({
                icon,
                title,
                text,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#0369a1',
            });

            return;
        }

        alert(text);
    };


    /*
    |--------------------------------------------------------------------------
    | BUTTON LOADING
    |--------------------------------------------------------------------------
    */

    const setButtonLoading = (
        $button,
        isLoading,
        label
    ) => {

        if (
            typeof window.setButtonLoading ===
            'function'
        ) {

            window.setButtonLoading(
                $button,
                isLoading,
                label
            );

            return;
        }

        $button.prop(
            'disabled',
            isLoading
        );

        $button.html(
            isLoading
                ? '<span class="inline-block size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Memproses'
                : label
        );
    };


    /*
    |--------------------------------------------------------------------------
    | INITIALIZE PANEL
    |--------------------------------------------------------------------------
    */

    const initPlugReeferPanel = () => {

        if (!hasPanel()) {
            return;
        }

        $('[data-handheld-menu]')
            .addClass('hidden');

        getNoContInput()
            .trigger('focus');
    };


    /*
    |--------------------------------------------------------------------------
    | EVENT HANDLERS
    |--------------------------------------------------------------------------
    */

    const bindPlugReeferHandlers = () => {


        /*
        |--------------------------------------------------------------------------
        | RELOAD DATA
        |--------------------------------------------------------------------------
        */

        $(document).off(
            'bos:reload-data.bosPlugReefer'
        );

        $(document).on(
            'bos:reload-data.bosPlugReefer',
            () => {

                const noCont =
                    getNoContInput()
                        .val()
                        ?.trim();

                if (
                    hasPanel() &&
                    noCont
                ) {
                    getSearchForm()
                        .trigger('submit');
                }
            }
        );


        /*
        |--------------------------------------------------------------------------
        | SEARCH CONTAINER
        |--------------------------------------------------------------------------
        */

        $(document).off(
            'submit.bosPlugReeferSearch',
            '[data-search-form]'
        );

        $(document).on(
            'submit.bosPlugReeferSearch',
            '[data-search-form]',
            function (event) {

                if (!hasPanel()) {
                    return;
                }

                event.preventDefault();

                const noCont =
                    getNoContInput()
                        .val()
                        ?.trim();

                const $button =
                    getSearchButton();

                const defaultLabel =
                    $button.data(
                        'default-label'
                    ) || $button.html();

                const searchUrl =
                    getSearchUrl();

                const csrfToken =
                    getCsrfToken();

                $button.data(
                    'default-label',
                    defaultLabel
                );


                /*
                |--------------------------------------------------------------------------
                | VALIDASI NOMOR CONTAINER
                |--------------------------------------------------------------------------
                */

                if (!noCont) {

                    showAlert(
                        'warning',
                        'Nomor Container kosong',
                        'Silakan isi nomor Container terlebih dahulu.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | VALIDASI URL
                |--------------------------------------------------------------------------
                */

                if (!searchUrl) {

                    showAlert(
                        'info',
                        'Dalam Pengembangan',
                        'Fitur pencarian Plug Reefer sedang dalam pengembangan.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | LOADING BUTTON
                |--------------------------------------------------------------------------
                */

                setButtonLoading(
                    $button,
                    true,
                    defaultLabel
                );


                /*
                |--------------------------------------------------------------------------
                | AJAX SEARCH
                |--------------------------------------------------------------------------
                */

                $.ajax({

                    url: searchUrl,

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },

                    data: {
                        no_cont: noCont,
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESS
                    |--------------------------------------------------------------------------
                    */

                    success: (response) => {

                        getTableContainer()
                            .empty()
                            .html(
                                response.data || ''
                            );

                        getDetailContainer()
                            .empty()
                            .addClass('hidden');

                        getResultWrapper()
                            .removeClass('hidden');
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | ERROR
                    |--------------------------------------------------------------------------
                    */

                    error: (xhr) => {

                        getResultWrapper()
                            .addClass('hidden');

                        const serverError =
                            xhr.status >= 500;


                        /*
                        |--------------------------------------------------------------------------
                        | CSRF ERROR
                        |--------------------------------------------------------------------------
                        */

                        if (xhr.status === 419) {

                            showAlert(
                                'warning',
                                'Sesi Tidak Valid',
                                'CSRF token tidak valid atau sesi telah berubah. Silakan refresh halaman terlebih dahulu.'
                            );

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | SEARCH ERROR
                        |--------------------------------------------------------------------------
                        */

                        showAlert(
                            serverError
                                ? 'error'
                                : 'info',

                            serverError
                                ? 'Terjadi Kesalahan'
                                : 'Container tidak ditemukan',

                            xhr.responseJSON?.message ||
                                'Gagal mencari container reefer.'
                        );
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | COMPLETE
                    |--------------------------------------------------------------------------
                    */

                    complete: () => {

                        setButtonLoading(
                            $button,
                            false,
                            defaultLabel
                        );
                    },

                });
            }
        );


        /*
        |--------------------------------------------------------------------------
        | RESET SEARCH
        |--------------------------------------------------------------------------
        |
        | Fungsi:
        | 1. Mengosongkan input pencarian.
        | 2. Menyembunyikan hasil pencarian.
        | 3. Menghapus tabel hasil pencarian.
        | 4. Menghapus detail container.
        | 5. Mengembalikan fokus ke input nomor container.
        |
        */

        $(document).off(
            'click.bosPlugReeferReset',
            '[data-reset-button]'
        );

        $(document).on(
            'click.bosPlugReeferReset',
            '[data-reset-button]',
            function (event) {

                if (!hasPanel()) {
                    return;
                }

                event.preventDefault();


                /*
                |--------------------------------------------------------------------------
                | RESET FORM PENCARIAN
                |--------------------------------------------------------------------------
                */

                const $searchForm =
                    getSearchForm();

                if (
                    $searchForm.length &&
                    $searchForm[0]
                ) {
                    $searchForm[0].reset();
                }


                /*
                |--------------------------------------------------------------------------
                | HIDE HASIL PENCARIAN
                |--------------------------------------------------------------------------
                */

                getResultWrapper()
                    .addClass('hidden');


                /*
                |--------------------------------------------------------------------------
                | CLEAR TABEL HASIL
                |--------------------------------------------------------------------------
                */

                getTableContainer()
                    .empty();


                /*
                |--------------------------------------------------------------------------
                | CLEAR DETAIL CONTAINER
                |--------------------------------------------------------------------------
                */

                getDetailContainer()
                    .empty()
                    .addClass('hidden');


                /*
                |--------------------------------------------------------------------------
                | FOCUS INPUT CONTAINER
                |--------------------------------------------------------------------------
                */

                getNoContInput()
                    .trigger('focus');
            }
        );


        /*
        |--------------------------------------------------------------------------
        | DETAIL CONTAINER
        |--------------------------------------------------------------------------
        */

        $(document).off(
            'click.bosPlugReeferDetail',
            '[data-detail-button]'
        );

        $(document).on(
            'click.bosPlugReeferDetail',
            '[data-detail-button]',
            function (event) {

                if (!hasPanel()) {
                    return;
                }

                event.preventDefault();

                const $button =
                    $(this);

                const noCont =
                    $button.data(
                        'no-cont'
                    );

                const detailUrl =
                    getDetailUrl();

                const csrfToken =
                    getCsrfToken();


                /*
                |--------------------------------------------------------------------------
                | VALIDASI NOMOR CONTAINER
                |--------------------------------------------------------------------------
                */

                if (!noCont) {

                    showAlert(
                        'warning',
                        'No Container kosong',
                        'No Container tidak ditemukan.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | VALIDASI URL DETAIL
                |--------------------------------------------------------------------------
                */

                if (!detailUrl) {

                    showAlert(
                        'info',
                        'Dalam Pengembangan',
                        'Detail Plug Reefer sedang dalam pengembangan.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | BUTTON LABEL
                |--------------------------------------------------------------------------
                */

                const defaultLabel =
                    $button.data(
                        'default-label'
                    ) || $button.html();

                $button.data(
                    'default-label',
                    defaultLabel
                );


                /*
                |--------------------------------------------------------------------------
                | LOADING BUTTON
                |--------------------------------------------------------------------------
                */

                setButtonLoading(
                    $button,
                    true,
                    defaultLabel
                );


                /*
                |--------------------------------------------------------------------------
                | AJAX DETAIL
                |--------------------------------------------------------------------------
                */

                $.ajax({

                    url: detailUrl,

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },

                    data: {
                        no_cont: noCont,
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESS
                    |--------------------------------------------------------------------------
                    */

                    success: (response) => {

                        getDetailContainer()
                            .removeClass('hidden')
                            .empty()
                            .html(
                                response.data || ''
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | SCROLL KE DETAIL
                        |--------------------------------------------------------------------------
                        */

                        const detail =
                            getDetailContainer()[0];

                        if (detail) {

                            detail.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start',
                            });
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | ERROR
                    |--------------------------------------------------------------------------
                    */

                    error: (xhr) => {

                        if (xhr.status === 419) {

                            showAlert(
                                'warning',
                                'Sesi Tidak Valid',
                                'CSRF token tidak valid atau sesi telah berubah. Silakan refresh halaman terlebih dahulu.'
                            );

                            return;
                        }

                        showAlert(
                            'error',
                            'Gagal',
                            xhr.responseJSON?.message ||
                                'Gagal mengambil detail Plug Reefer.'
                        );
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | COMPLETE
                    |--------------------------------------------------------------------------
                    */

                    complete: () => {

                        setButtonLoading(
                            $button,
                            false,
                            defaultLabel
                        );
                    },

                });
            }
        );


        /*
        |--------------------------------------------------------------------------
        | SUBMIT PLUG / UNPLUG REEFER
        |--------------------------------------------------------------------------
        */

        $(document).off(
            'submit.bosPlugReeferSend',
            '[data-send-form]'
        );

        $(document).on(
            'submit.bosPlugReeferSend',
            '[data-send-form]',
            function (event) {

                if (!hasPanel()) {
                    return;
                }

                event.preventDefault();

                const $form =
                    $(this);

                const $button =
                    $form.find(
                        'button[type="submit"]'
                    );

                const defaultLabel =
                    $button.data(
                        'default-label'
                    ) || $button.html();

                const storeUrl =
                    getStoreUrl();

                const csrfToken =
                    getCsrfToken();

                $button.data(
                    'default-label',
                    defaultLabel
                );


                /*
                |--------------------------------------------------------------------------
                | VALIDASI URL STORE
                |--------------------------------------------------------------------------
                */

                if (!storeUrl) {

                    showAlert(
                        'info',
                        'Dalam Pengembangan',
                        'Fitur Plugin Reefer sedang dalam pengembangan.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | LOADING BUTTON
                |--------------------------------------------------------------------------
                */

                setButtonLoading(
                    $button,
                    true,
                    defaultLabel
                );


                /*
                |--------------------------------------------------------------------------
                | AJAX STORE
                |--------------------------------------------------------------------------
                */

                $.ajax({

                    url: storeUrl,

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },

                    data: $form.serialize(),


                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESS
                    |--------------------------------------------------------------------------
                    */

                    success: (response) => {

                        showAlert(
                            'success',
                            'Berhasil',
                            response.message ||
                                'Data berhasil diproses.'
                        );
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | ERROR
                    |--------------------------------------------------------------------------
                    */

                    error: (xhr) => {

                        if (xhr.status === 419) {

                            showAlert(
                                'warning',
                                'Sesi Tidak Valid',
                                'CSRF token tidak valid atau sesi telah berubah. Silakan refresh halaman terlebih dahulu.'
                            );

                            return;
                        }

                        showAlert(
                            'error',
                            'Gagal',
                            xhr.responseJSON?.message ||
                                'Data gagal diproses.'
                        );
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | COMPLETE
                    |--------------------------------------------------------------------------
                    */

                    complete: () => {

                        setButtonLoading(
                            $button,
                            false,
                            defaultLabel
                        );
                    },

                });
            }
        );


        /*
        |--------------------------------------------------------------------------
        | CLOSE DETAIL
        |--------------------------------------------------------------------------
        */

        $(document).off(
            'click.bosPlugReeferClose',
            '[data-close-detail]'
        );

        $(document).on(
            'click.bosPlugReeferClose',
            '[data-close-detail]',
            function () {

                getDetailContainer()
                    .empty()
                    .addClass('hidden');
            }
        );
    };


    /*
    |--------------------------------------------------------------------------
    | INITIALIZE EVENT HANDLERS
    |--------------------------------------------------------------------------
    */

    bindPlugReeferHandlers();

    initPlugReeferPanel();


    /*
    |--------------------------------------------------------------------------
    | DOCUMENT READY
    |--------------------------------------------------------------------------
    */

    $(() => {
        initPlugReeferPanel();
    });


    /*
    |--------------------------------------------------------------------------
    | LIVEWIRE NAVIGATION
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'livewire:navigated',
        () => {

            bindPlugReeferHandlers();

            initPlugReeferPanel();
        }
    );

})(window.jQuery);