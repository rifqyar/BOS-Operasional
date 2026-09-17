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


    const initPlugReeferPanel = () => {

        if (!hasPanel()) {
            return;
        }

        $('[data-handheld-menu]')
            .addClass('hidden');

        getNoContInput()
            .trigger('focus');
    };


    const bindPlugReeferHandlers = () => {

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


                if (!noCont) {

                    showAlert(
                        'warning',
                        'Nomor Container kosong',
                        'Silakan isi nomor Container terlebih dahulu.'
                    );

                    return;
                }


                if (!searchUrl) {

                    showAlert(
                        'info',
                        'Dalam Pengembangan',
                        'Fitur pencarian Plug Reefer sedang dalam pengembangan.'
                    );

                    return;
                }


                setButtonLoading(
                    $button,
                    true,
                    defaultLabel
                );


                $.ajax({

                    url: searchUrl,

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },

                    data: {
                        no_cont: noCont,
                    },

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

                    error: (xhr) => {

                        getResultWrapper()
                            .addClass('hidden');

                        const serverError =
                            xhr.status >= 500;

                        if (xhr.status === 419) {

                            showAlert(
                                'warning',
                                'Sesi Tidak Valid',
                                'CSRF token tidak valid atau sesi telah berubah. Silakan refresh halaman terlebih dahulu.'
                            );

                            return;
                        }

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


                if (!noCont) {

                    showAlert(
                        'warning',
                        'No Container kosong',
                        'No Container tidak ditemukan.'
                    );

                    return;
                }


                if (!detailUrl) {

                    showAlert(
                        'info',
                        'Dalam Pengembangan',
                        'Detail Plug Reefer sedang dalam pengembangan.'
                    );

                    return;
                }


                const defaultLabel =
                    $button.data(
                        'default-label'
                    ) || $button.html();

                $button.data(
                    'default-label',
                    defaultLabel
                );


                setButtonLoading(
                    $button,
                    true,
                    defaultLabel
                );


                $.ajax({

                    url: detailUrl,

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },

                    data: {
                        no_cont: noCont,
                    },

                    success: (response) => {

                        getDetailContainer()
                            .removeClass('hidden')
                            .empty()
                            .html(
                                response.data || ''
                            );

                        const detail =
                            getDetailContainer()[0];

                        if (detail) {

                            detail.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start',
                            });
                        }
                    },

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


                if (!storeUrl) {

                    showAlert(
                        'info',
                        'Dalam Pengembangan',
                        'Fitur Plugin Reefer sedang dalam pengembangan.'
                    );

                    return;
                }


                setButtonLoading(
                    $button,
                    true,
                    defaultLabel
                );


                $.ajax({

                    url: storeUrl,

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },

                    data: $form.serialize(),

                    success: (response) => {

                        showAlert(
                            'success',
                            'Berhasil',
                            response.message ||
                                'Data berhasil diproses.'
                        );
                    },

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


    bindPlugReeferHandlers();

    initPlugReeferPanel();


    $(() => {
        initPlugReeferPanel();
    });


    $(document).on(
        'livewire:navigated',
        () => {

            bindPlugReeferHandlers();

            initPlugReeferPanel();
        }
    );

})(window.jQuery);