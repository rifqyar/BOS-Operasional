(function ($) {
    'use strict';


    /*
     * =========================================================
     * GET PANEL
     * =========================================================
     */

    function getPanel() {
        return $('[data-panel][data-panel-name="marshallingcic"]').first();
    }


    /*
     * =========================================================
     * ALERT
     * =========================================================
     */

    function showAlert(message, type = 'error') {

        if (typeof window.showAlert === 'function') {
            window.showAlert(message, type);
            return;
        }


        if (typeof Swal !== 'undefined') {

            Swal.fire({
                icon: type === 'success'
                    ? 'success'
                    : 'error',

                text: message,
            });

            return;
        }


        alert(message);
    }


    /*
     * =========================================================
     * BUTTON LOADING
     * =========================================================
     */

    function setLoading(
        $button,
        loading,
        text = 'Loading...'
    ) {

        if (
            typeof window.setButtonLoading ===
            'function'
        ) {

            window.setButtonLoading(
                $button,
                loading,
                text
            );

            return;
        }


        if (
            !$button ||
            !$button.length
        ) {
            return;
        }


        if (loading) {

            $button.data(
                'original-text',
                $button.text()
            );


            $button.prop(
                'disabled',
                true
            );


            $button.text(
                text
            );

        } else {

            $button.prop(
                'disabled',
                false
            );


            $button.text(
                $button.data(
                    'original-text'
                ) || 'Search'
            );
        }
    }


    /*
     * =========================================================
     * LOAD INITIAL TABLE / PAGINATION
     * =========================================================
     */

    function loadInitialTable(page = 1) {

        const $panel =
            getPanel();


        if (!$panel.length) {
            return;
        }


        const url =
            $panel.data(
                'data-url'
            );


        if (!url) {
            return;
        }


        const $container =
            $panel.find(
                '[data-table-container]'
            );


        /*
         * Per page default 10.
         *
         * Bisa diambil dari panel jika nanti
         * ingin dibuat configurable.
         */

        const perPage =
            parseInt(
                $panel.data(
                    'per-page'
                ),
                10
            ) || 10;


        $.ajax({

            url: url,

            type: 'GET',

            dataType: 'json',

            data: {

                page: page,

                per_page: perPage,
            },


            success: function (response) {

                if (response.data) {

                    $container.html(
                        response.data
                    );
                }
            },


            error: function (xhr) {

                const message =
                    xhr.responseJSON?.message ||
                    'Gagal mengambil data Marshalling CIC.';


                $container.html(`

                    <div
                        class="rounded-2xl bg-white p-6 text-center
                               shadow-sm ring-1 ring-red-200
                               dark:bg-slate-900
                               dark:ring-red-900"
                    >

                        <p
                            class="text-sm text-red-600
                                   dark:text-red-400"
                        >
                            ${message}
                        </p>

                    </div>

                `);
            },

        });
    }


    /*
     * =========================================================
     * LOAD PAGE
     * =========================================================
     *
     * Dipanggil ketika tombol pagination diklik.
     * =========================================================
     */

    function loadPage(page) {

        const pageNumber =
            parseInt(
                page,
                10
            );


        if (
            !pageNumber ||
            pageNumber < 1
        ) {
            return;
        }


        loadInitialTable(
            pageNumber
        );


        /*
         * Scroll kembali ke area table.
         */

        const $panel =
            getPanel();


        if (!$panel.length) {
            return;
        }


        const $container =
            $panel.find(
                '[data-table-container]'
            );


        if ($container.length) {

            $('html, body').animate(
                {
                    scrollTop:
                        $container.offset().top - 20,
                },
                250
            );
        }
    }


    /*
     * =========================================================
     * SEARCH CONTAINER
     * =========================================================
     */

    function searchContainer(form) {

        const $panel =
            getPanel();


        if (!$panel.length) {
            return;
        }


        const url =
            $panel.data(
                'search-url'
            );


        if (!url) {
            return;
        }


        const $form =
            $(form);


        const $input =
            $form.find(
                '[name="no_cont"]'
            );


        const $button =
            $form.find(
                '[data-search-button]'
            );


        const noCont =
            $.trim(
                $input.val() || ''
            ).toUpperCase();


        if (!noCont) {

            showAlert(
                'Nomor Container wajib diisi.'
            );


            $input.trigger(
                'focus'
            );


            return;
        }


        setLoading(
            $button,
            true,
            'Mencari...'
        );


        $.ajax({

            url: url,

            type: 'POST',

            dataType: 'json',

            data: {

                no_cont:
                    noCont,

                _token:
                    $panel.data(
                        'csrf-token'
                    ),
            },


            success: function (response) {

                if (response.data) {

                    $panel
                        .find(
                            '[data-table-container]'
                        )
                        .html(
                            response.data
                        );


                    $panel
                        .find(
                            '[data-detail-container]'
                        )
                        .addClass(
                            'hidden'
                        )
                        .empty();
                }
            },


            error: function (xhr) {

                const message =
                    xhr.responseJSON?.message ||
                    'Data Container tidak ditemukan.';


                showAlert(
                    message
                );
            },


            complete: function () {

                setLoading(
                    $button,
                    false
                );
            },

        });
    }


    /*
     * =========================================================
     * LOAD DETAIL / PROSES MARSHALLING
     *
     * FLOW LAMA.
     * JANGAN DIUBAH.
     * =========================================================
     */

    function loadDetail(
        idJobSlip,
        $button
    ) {

        const $panel =
            getPanel();


        if (!$panel.length) {
            return;
        }


        const url =
            $panel.data(
                'detail-url'
            );


        if (!url) {
            return;
        }


        $button.prop(
            'disabled',
            true
        );


        const originalText =
            $button.text();


        $button.text(
            'Loading...'
        );


        $.ajax({

            url: url,

            type: 'POST',

            dataType: 'json',

            data: {

                id_job_slip:
                    idJobSlip,

                _token:
                    $panel.data(
                        'csrf-token'
                    ),
            },


            success: function (response) {

                if (response.data) {

                    const $detail =
                        $panel.find(
                            '[data-detail-container]'
                        );


                    $detail
                        .html(
                            response.data
                        )
                        .removeClass(
                            'hidden'
                        );


                    $('html, body').animate(
                        {
                            scrollTop:
                                $detail.offset().top - 20,
                        },
                        300
                    );
                }
            },


            error: function (xhr) {

                const message =
                    xhr.responseJSON?.message ||
                    'Detail Marshalling CIC tidak ditemukan.';


                showAlert(
                    message
                );
            },


            complete: function () {

                $button.prop(
                    'disabled',
                    false
                );


                $button.text(
                    originalText
                );
            },

        });
    }


    /*
     * =========================================================
     * MOBILE DETAIL (!)
     *
     * ! hanya membuka / menutup detail.
     *
     * TIDAK memanggil loadDetail().
     * TIDAK menjalankan PROSES.
     * =========================================================
     */

    function toggleMobileDetail(
        button
    ) {

        const $button =
            $(button);


        const number =
            $button.data(
                'cic-mobile-detail'
            );


        if (
            number === undefined ||
            number === null
        ) {
            return;
        }


        const $panel =
            getPanel();


        if (!$panel.length) {
            return;
        }


        const $detail =
            $panel.find(
                `[data-cic-mobile-detail-row="${number}"]`
            );


        if (!$detail.length) {
            return;
        }


        const isHidden =
            $detail.hasClass(
                'hidden'
            );


        if (isHidden) {

            /*
             * OPEN
             */

            $detail.removeClass(
                'hidden'
            );


            $button.attr(
                'aria-expanded',
                'true'
            );


            $button.addClass(
                'bg-amber-100',
                'dark:bg-amber-400/10'
            );


        } else {

            /*
             * CLOSE
             */

            $detail.addClass(
                'hidden'
            );


            $button.attr(
                'aria-expanded',
                'false'
            );


            $button.removeClass(
                'bg-amber-100',
                'dark:bg-amber-400/10'
            );
        }
    }


    /*
     * =========================================================
     * RESET SEARCH
     * =========================================================
     */

    function resetSearch() {

        const $panel =
            getPanel();


        if (!$panel.length) {
            return;
        }


        $panel
            .find(
                '[name="no_cont"]'
            )
            .val('');


        $panel
            .find(
                '[data-detail-container]'
            )
            .addClass(
                'hidden'
            )
            .empty();


        /*
         * Kembali ke page 1.
         */

        loadInitialTable(
            1
        );
    }


    /*
     * =========================================================
     * BIND EVENTS
     * =========================================================
     */

    function bindEvents() {


        /*
         * -----------------------------------------------------
         * SEARCH
         * -----------------------------------------------------
         */

        $(document)
            .off(
                'submit.marshallingcic',
                '[data-panel-name="marshallingcic"] [data-search-form]'
            )
            .on(
                'submit.marshallingcic',
                '[data-panel-name="marshallingcic"] [data-search-form]',
                function (event) {

                    event.preventDefault();


                    searchContainer(
                        this
                    );
                }
            );


        /*
         * -----------------------------------------------------
         * RESET
         * -----------------------------------------------------
         */

        $(document)
            .off(
                'click.marshallingcic',
                '[data-panel-name="marshallingcic"] [data-reset-button]'
            )
            .on(
                'click.marshallingcic',
                '[data-panel-name="marshallingcic"] [data-reset-button]',
                function () {

                    resetSearch();
                }
            );


        /*
         * -----------------------------------------------------
         * PAGINATION
         * -----------------------------------------------------
         */

        $(document)
            .off(
                'click.marshallingcic.pagination',
                '[data-panel-name="marshallingcic"] [data-cic-page]'
            )
            .on(
                'click.marshallingcic.pagination',
                '[data-panel-name="marshallingcic"] [data-cic-page]',
                function (event) {

                    event.preventDefault();


                    const page =
                        $(this).data(
                            'cic-page'
                        );


                    if (!page) {
                        return;
                    }


                    loadPage(
                        page
                    );
                }
            );


        /*
         * -----------------------------------------------------
         * MOBILE DETAIL (!)
         *
         * HARUS DIHANDLE TERLEBIH DAHULU.
         *
         * Supaya tombol ! tidak dianggap sebagai
         * tombol PROSES.
         * -----------------------------------------------------
         */

        $(document)
            .off(
                'click.marshallingcic.mobileDetail',
                '[data-panel-name="marshallingcic"] [data-cic-mobile-detail]'
            )
            .on(
                'click.marshallingcic.mobileDetail',
                '[data-panel-name="marshallingcic"] [data-cic-mobile-detail]',
                function () {

                    toggleMobileDetail(
                        this
                    );
                }
            );


        /*
         * -----------------------------------------------------
         * PROSES MARSHALLING CIC
         *
         * FLOW LAMA TETAP.
         * -----------------------------------------------------
         */

        $(document)
            .off(
                'click.marshallingcic',
                '[data-panel-name="marshallingcic"] [data-detail-id]'
            )
            .on(
                'click.marshallingcic',
                '[data-panel-name="marshallingcic"] [data-detail-id]',
                function () {

                    const $button =
                        $(this);


                    const idJobSlip =
                        $button.data(
                            'detail-id'
                        );


                    if (!idJobSlip) {
                        return;
                    }


                    loadDetail(
                        idJobSlip,
                        $button
                    );
                }
            );
    }


    /*
     * =========================================================
     * LIVEWIRE NAVIGATION
     * =========================================================
     */

    $(document).on(
        'livewire:navigated.marshallingcic',
        function () {

            bindEvents();

            loadInitialTable(
                1
            );
        }
    );


    /*
     * =========================================================
     * INITIAL PAGE LOAD
     * =========================================================
     */

    $(function () {

        bindEvents();

        loadInitialTable(
            1
        );
    });


})(jQuery);