(($) => {

    if (!$) {
        return;
    }

    const getPanel = () =>
        $('[data-panel][data-panel-name="realisasi"]').first();

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

    const getRowsContainer = () =>
        $('[data-table]', getPanel());

    const getDetailContainer = () =>
        $('[data-detail]', getPanel());

    const getSearchUrl = () =>
        getPanel().data('searchUrl');

    const getDetailUrl = () =>
        getPanel().data('detailUrl');

    const getStoreUrl = () =>
        getPanel().data('storeUrl');

    /*
    |--------------------------------------------------------------------------
    | CSRF TOKEN
    |--------------------------------------------------------------------------
    |
    | Token diambil langsung dari attribute:
    |
    | data-csrf-token="{{ csrf_token() }}"
    |
    | pada panel Realisasi.
    |
    */

    const getCsrfToken = () =>
        getPanel().attr('data-csrf-token') || '';

    /*
    |--------------------------------------------------------------------------
    | AJAX POST HELPER
    |--------------------------------------------------------------------------
    |
    | Semua POST Realisasi menggunakan helper ini agar CSRF
    | selalu dikirim ke Laravel.
    |
    */

    const postAjax = (url, options = {}) => {

        const csrfToken = getCsrfToken();

        return $.ajax({
            ...options,

            url,
            method: 'POST',

            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                ...(options.headers || {}),
            },

            xhrFields: {
                withCredentials: true,
                ...(options.xhrFields || {}),
            },
        });
    };

    const showAlert = (icon, title, text) => {

        if (typeof window.showAlert === 'function') {

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

    const initRealisasiPanel = () => {

        if (hasPanel()) {

            $('[data-handheld-menu]')
                .addClass('hidden');

            getNoContInput()
                .trigger('focus');
        }
    };

    const bindRealisasiHandlers = () => {

        /*
        |--------------------------------------------------------------------------
        | RELOAD DATA
        |--------------------------------------------------------------------------
        */

        $(document).off(
            'bos:reload-data.bosRealisasi'
        );

        $(document).on(
            'bos:reload-data.bosRealisasi',
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
        | SEARCH
        |--------------------------------------------------------------------------
        */

        $(document).off(
            'submit.bosRealisasiSearch',
            '[data-search-form]'
        );

        $(document).on(
            'submit.bosRealisasiSearch',
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
                    $button.data('default-label') ||
                    $button.html();

                const searchUrl =
                    getSearchUrl();

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
                        'Fitur pencarian Realisasi sedang dalam pengembangan.'
                    );

                    return;
                }

                setButtonLoading(
                    $button,
                    true,
                    defaultLabel
                );

                postAjax(
                    searchUrl,
                    {
                        data: {
                            no_cont: noCont,
                        },
                    }
                )

                    .done((response) => {

                        getRowsContainer()
                            .empty()
                            .html(
                                response.data || ''
                            );

                        getResultWrapper()
                            .removeClass('hidden');

                        getDetailContainer()
                            .empty()
                            .addClass('hidden');
                    })

                    .fail((xhr) => {

                        getResultWrapper()
                            .addClass('hidden');

                        const isServerError =
                            xhr.status >= 500;

                        showAlert(
                            isServerError
                                ? 'error'
                                : 'info',

                            isServerError
                                ? 'Terjadi Kesalahan'
                                : 'Container tidak ditemukan',

                            xhr.responseJSON?.message ||
                                'Gagal mengambil data container, harap hubungi tim IT'
                        );
                    })

                    .always(() => {

                        setButtonLoading(
                            $button,
                            false,
                            defaultLabel
                        );
                    });
            }
        );

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $(document).off(
            'click.bosRealisasiDetail',
            '[data-detail-button]'
        );

        $(document).on(
            'click.bosRealisasiDetail',
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
                        'Fitur detail Realisasi sedang dalam pengembangan.'
                    );

                    return;
                }

                const defaultLabel =
                    $button.data('default-label') ||
                    $button.html();

                $button.data(
                    'default-label',
                    defaultLabel
                );

                setButtonLoading(
                    $button,
                    true,
                    defaultLabel
                );

                postAjax(
                    detailUrl,
                    {
                        data: {
                            no_cont: noCont,
                        },
                    }
                )

                    .done((response) => {

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
                    })

                    .fail((xhr) => {

                        showAlert(
                            'error',
                            'Gagal',
                            xhr.responseJSON?.message ||
                                'Gagal mengambil detail pemeriksaan.'
                        );
                    })

                    .always(() => {

                        setButtonLoading(
                            $button,
                            false,
                            defaultLabel
                        );
                    });
            }
        );

        /*
        |--------------------------------------------------------------------------
        | STORE / SEND
        |--------------------------------------------------------------------------
        */

        $(document).off(
            'submit.bosRealisasiSend',
            '[data-send-form]'
        );

        $(document).on(
            'submit.bosRealisasiSend',
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
                    $button.data('default-label') ||
                    $button.html();

                const storeUrl =
                    getStoreUrl();

                $button.data(
                    'default-label',
                    defaultLabel
                );

                if (!storeUrl) {

                    showAlert(
                        'info',
                        'Dalam Pengembangan',
                        'Fitur simpan Realisasi sedang dalam pengembangan.'
                    );

                    return;
                }

                setButtonLoading(
                    $button,
                    true,
                    defaultLabel
                );

                postAjax(
                    storeUrl,
                    {
                        data: $form.serialize(),
                    }
                )

                    .done((response) => {

                        showAlert(
                            'success',
                            'Berhasil',
                            response.message ||
                                'Data berhasil diproses.'
                        );
                    })

                    .fail((xhr) => {

                        showAlert(
                            'error',
                            'Gagal',
                            xhr.responseJSON?.message ||
                                'Data gagal diproses.'
                        );
                    })

                    .always(() => {

                        setButtonLoading(
                            $button,
                            false,
                            defaultLabel
                        );
                    });
            }
        );

        /*
        |--------------------------------------------------------------------------
        | CLOSE DETAIL
        |--------------------------------------------------------------------------
        */

        $(document).off(
            'click.bosRealisasiClose',
            '[data-close-detail]'
        );

        $(document).on(
            'click.bosRealisasiClose',
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
    | INITIALIZE
    |--------------------------------------------------------------------------
    */

    bindRealisasiHandlers();

    initRealisasiPanel();

    $(() => {

        initRealisasiPanel();
    });

    /*
    |--------------------------------------------------------------------------
    | LIVEWIRE NAVIGATION
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'livewire:navigated',
        () => {

            bindRealisasiHandlers();no

            initRealisasiPanel();
        }
    );

})(window.jQuery);