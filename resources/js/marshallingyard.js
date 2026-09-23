(function ($) {
    'use strict';


    /*
     * =========================================================
     * PANEL
     * =========================================================
     */

    const PANEL_SELECTOR =
        '[data-panel][data-panel-name="marshallingyard"]';


    function getPanel() {
        return document.querySelector(PANEL_SELECTOR);
    }


    function getUrl(panel, attribute) {
        return panel?.dataset?.[attribute] || '';
    }


    function getCsrf(panel) {
        return panel?.dataset?.csrfToken || '';
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


        if (window.Swal) {

            Swal.fire({
                icon: type === 'success'
                    ? 'success'
                    : 'error',

                text: message,
            });

            return;
        }


        window.alert(message);
    }


    /*
     * =========================================================
     * BUTTON LOADING
     * =========================================================
     */

    function setLoading(
        button,
        loading,
        textLoading = 'Memproses...'
    ) {

        if (!button) {
            return;
        }


        if (loading) {

            button.dataset.originalText =
                button.innerHTML;

            button.innerHTML =
                textLoading;

            button.disabled = true;

        } else {

            button.innerHTML =
                button.dataset.originalText ||
                'Search';

            button.disabled = false;
        }
    }


    /*
     * =========================================================
     * DATA COUNT
     * =========================================================
     */

    function setDataCount(
        panel,
        count
    ) {

        const element =
            panel.querySelector(
                '[data-data-count]'
            );


        if (!element) {
            return;
        }


        element.textContent =
            `${count} data`;
    }


    /*
     * =========================================================
     * LOAD INITIAL DATA
     *
     * Pagination:
     * page = halaman aktif
     * per_page = jumlah data
     * =========================================================
     */

    function loadInitialData(
        panel,
        page = 1
    ) {

        const url =
            getUrl(
                panel,
                'dataUrl'
            );


        if (!url) {
            return;
        }


        const table =
            panel.querySelector(
                '[data-table]'
            );


        if (!table) {
            return;
        }


        table.innerHTML = `
            <div
                class="px-4 py-10 text-center
                       text-sm text-slate-500"
            >
                Memuat data Marshalling Yard...
            </div>
        `;


        $.ajax({

            url: url,

            method: 'GET',

            data: {
                page: page,
                per_page: 10,
            },

            headers: {

                'X-CSRF-TOKEN':
                    getCsrf(panel),

                'Accept':
                    'application/json',
            },

        })

            .done(function (response) {

                table.innerHTML =
                    response.data || '';


                /*
                 * Untuk paginator,
                 * count berasal dari metadata.
                 */
                if (
                    response.pagination &&
                    typeof response.pagination.total !==
                        'undefined'
                ) {

                    setDataCount(
                        panel,
                        response.pagination.total
                    );

                } else {

                    setDataCount(
                        panel,
                        countRows(
                            response.data || ''
                        )
                    );
                }
            })

            .fail(function (xhr) {

                const message =
                    xhr.responseJSON?.message ||
                    'Gagal mengambil data Marshalling Yard.';


                table.innerHTML = `
                    <div
                        class="px-4 py-10 text-center
                               text-sm text-rose-600"
                    >
                        ${message}
                    </div>
                `;


                setDataCount(
                    panel,
                    0
                );
            });
    }


    /*
     * =========================================================
     * COUNT ROWS
     * =========================================================
     */

    function countRows(html) {

        const temp =
            document.createElement('div');


        temp.innerHTML =
            html;


        /*
         * Jangan menghitung detail row mobile
         * sebagai data utama.
         */
        return temp.querySelectorAll(
            'tbody > tr:not([data-yard-mobile-detail-row])'
        ).length;
    }


    /*
     * =========================================================
     * LOAD PAGE
     * =========================================================
     */

    function loadPage(
        panel,
        page
    ) {

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


        loadInitialData(
            panel,
            pageNumber
        );


        /*
         * Scroll ke table setelah
         * user berpindah halaman.
         */
        const table =
            panel.querySelector(
                '[data-table]'
            );


        if (table) {

            setTimeout(
                function () {

                    table.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                    });

                },
                150
            );
        }
    }


    /*
     * =========================================================
     * SEARCH DATA
     * =========================================================
     */

    function searchData(
        panel,
        form
    ) {

        const url =
            getUrl(
                panel,
                'searchUrl'
            );


        if (!url) {
            return;
        }


        const button =
            form.querySelector(
                '[data-search-button]'
            );


        const noCont =
            form.querySelector(
                '[name="no_cont"]'
            )?.value
                ?.trim() || '';


        if (!noCont) {

            showAlert(
                'Nomor container wajib diisi.',
                'error'
            );


            return;
        }


        setLoading(
            button,
            true,
            'Mencari...'
        );


        $.ajax({

            url: url,

            method: 'POST',

            data: {
                no_cont: noCont,

                page: 1,

                per_page: 10,
            },

            headers: {

                'X-CSRF-TOKEN':
                    getCsrf(panel),

                'Accept':
                    'application/json',
            },

        })

            .done(function (response) {

                const table =
                    panel.querySelector(
                        '[data-table]'
                    );


                if (table) {

                    table.innerHTML =
                        response.data || '';
                }


                if (
                    response.pagination &&
                    typeof response.pagination.total !==
                        'undefined'
                ) {

                    setDataCount(
                        panel,
                        response.pagination.total
                    );

                } else {

                    setDataCount(
                        panel,
                        countRows(
                            response.data || ''
                        )
                    );
                }


                /*
                 * Response message hanya ditampilkan
                 * jika memang ada.
                 */
                if (
                    response.message &&
                    response.success === false
                ) {

                    showAlert(
                        response.message,
                        'error'
                    );
                }
            })

            .fail(function (xhr) {

                const message =
                    xhr.responseJSON?.message ||
                    'Gagal mencari data Marshalling Yard.';


                showAlert(
                    message,
                    'error'
                );


                /*
                 * Jika search tidak menemukan data,
                 * tetap render response table kosong
                 * bila server mengirimkannya.
                 */
                if (
                    xhr.responseJSON?.data
                ) {

                    const table =
                        panel.querySelector(
                            '[data-table]'
                        );


                    if (table) {

                        table.innerHTML =
                            xhr.responseJSON.data;
                    }
                }
            })

            .always(function () {

                setLoading(
                    button,
                    false,
                    'Search'
                );
            });
    }


    /*
     * =========================================================
     * LOAD DETAIL / PROSES MARSHALLING YARD
     * =========================================================
     */

    function loadDetail(
        panel,
        button
    ) {

        const url =
            getUrl(
                panel,
                'detailUrl'
            );


        if (!url) {
            return;
        }


        const idJobSlip =
            button.dataset.idJobSlip;


        if (!idJobSlip) {

            showAlert(
                'ID Job Slip tidak ditemukan.',
                'error'
            );


            return;
        }


        const detail =
            panel.querySelector(
                '[data-detail]'
            );


        if (!detail) {
            return;
        }


        button.disabled = true;


        button.dataset.originalText =
            button.innerHTML;


        button.innerHTML =
            'Memuat...';


        detail.classList.remove(
            'hidden'
        );


        detail.innerHTML = `
            <div
                class="rounded-2xl bg-white p-6 text-center
                       shadow-sm ring-1 ring-slate-200
                       dark:bg-slate-900
                       dark:ring-slate-800"
            >

                <div
                    class="text-sm text-slate-500"
                >
                    Memuat detail Marshalling Yard...
                </div>

            </div>
        `;


        detail.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });


        $.ajax({

            url: url,

            method: 'POST',

            data: {

                id_job_slip:
                    idJobSlip,
            },

            headers: {

                'X-CSRF-TOKEN':
                    getCsrf(panel),

                'Accept':
                    'application/json',
            },

        })

            .done(function (response) {

                detail.innerHTML =
                    response.data || '';


                bindDependentFields(
                    detail
                );
            })

            .fail(function (xhr) {

                const message =
                    xhr.responseJSON?.message ||
                    'Gagal mengambil detail Marshalling Yard.';


                detail.innerHTML = `
                    <div
                        class="rounded-2xl
                               border border-rose-200
                               bg-rose-50 px-4 py-5
                               text-sm text-rose-700"
                    >
                        ${message}
                    </div>
                `;


                showAlert(
                    message,
                    'error'
                );
            })

            .always(function () {

                button.innerHTML =
                    button.dataset.originalText ||
                    'PROSES';


                button.disabled = false;
            });
    }


    /*
     * =========================================================
     * MOBILE DETAIL
     *
     * Tombol "!" hanya membuka / menutup
     * detail row mobile.
     *
     * Tidak menjalankan PROSES.
     * =========================================================
     */

    function toggleMobileDetail(
        panel,
        button
    ) {

        const number =
            button.dataset.yardMobileDetail;


        if (!number) {
            return;
        }


        const detail =
            panel.querySelector(
                `[data-yard-mobile-detail-row="${number}"]`
            );


        if (!detail) {
            return;
        }


        const isHidden =
            detail.classList.contains(
                'hidden'
            );


        if (isHidden) {

            detail.classList.remove(
                'hidden'
            );


            button.setAttribute(
                'aria-expanded',
                'true'
            );


            button.classList.add(
                'bg-amber-100',
                'dark:bg-amber-400/10'
            );

        } else {

            detail.classList.add(
                'hidden'
            );


            button.setAttribute(
                'aria-expanded',
                'false'
            );


            button.classList.remove(
                'bg-amber-100',
                'dark:bg-amber-400/10'
            );
        }
    }


    /*
     * =========================================================
     * DEPENDENT FIELDS
     * =========================================================
     */

    function bindDependentFields(
        scope
    ) {

        $(scope)
            .find('[data-activity]')
            .off(
                'change.marshallingyard'
            )
            .on(
                'change.marshallingyard',
                function () {

                    const activity =
                        String(
                            this.value || ''
                        );


                    const group =
                        this.dataset.activity;


                    const dependent =
                        scope.querySelectorAll(
                            `[data-dependent="${group}"]`
                        );


                    dependent.forEach(
                        function (element) {

                            const enabled =
                                activity !== '' &&
                                activity !== '0';


                            element.disabled =
                                !enabled;


                            if (!enabled) {

                                element.value =
                                    '';
                            }
                        }
                    );
                }
            );


        $(scope)
            .find('[data-activity]')
            .trigger(
                'change.marshallingyard'
            );
    }


    /*
     * =========================================================
     * STORE DATA
     * =========================================================
     */

    function storeData(
        panel,
        form
    ) {

        const url =
            getUrl(
                panel,
                'storeUrl'
            );


        if (!url) {
            return;
        }


        const button =
            form.querySelector(
                '[data-store-button]'
            );


        const formData =
            $(form).serialize();


        setLoading(
            button,
            true,
            'Menyimpan...'
        );


        $.ajax({

            url: url,

            method: 'POST',

            data: formData,

            headers: {

                'X-CSRF-TOKEN':
                    getCsrf(panel),

                'Accept':
                    'application/json',
            },

        })

            .done(function (response) {

                showAlert(
                    response.message ||
                    'Data Marshalling Yard berhasil divalidasi.',
                    'success'
                );
            })

            .fail(function (xhr) {

                const message =
                    xhr.responseJSON?.message ||
                    'Gagal memproses Marshalling Yard.';


                showAlert(
                    message,
                    'error'
                );
            })

            .always(function () {

                setLoading(
                    button,
                    false,
                    'Simpan Marshalling Yard'
                );
            });
    }


    /*
     * =========================================================
     * BIND PANEL
     * =========================================================
     */

    function bindPanel(
        panel
    ) {

        if (
            !panel ||
            panel.dataset.bound === '1'
        ) {
            return;
        }


        panel.dataset.bound = '1';


        const namespace =
            '.marshallingyard';


        /*
         * -----------------------------------------------------
         * SEARCH
         * -----------------------------------------------------
         */

        $(panel)
            .off(
                `submit${namespace}`,
                '[data-search-form]'
            )
            .on(
                `submit${namespace}`,
                '[data-search-form]',
                function (event) {

                    event.preventDefault();


                    searchData(
                        panel,
                        this
                    );
                }
            );


        /*
         * -----------------------------------------------------
         * RESET
         * -----------------------------------------------------
         */

        $(panel)
            .off(
                `click${namespace}`,
                '[data-reset-button]'
            )
            .on(
                `click${namespace}`,
                '[data-reset-button]',
                function () {

                    const input =
                        panel.querySelector(
                            '[name="no_cont"]'
                        );


                    if (input) {

                        input.value = '';

                        input.focus();
                    }


                    loadInitialData(
                        panel,
                        1
                    );
                }
            );


        /*
         * -----------------------------------------------------
         * PAGINATION
         * -----------------------------------------------------
         */

        $(panel)
            .off(
                `click${namespace}.pagination`,
                '[data-yard-page]'
            )
            .on(
                `click${namespace}.pagination`,
                '[data-yard-page]',
                function (event) {

                    event.preventDefault();


                    const page =
                        this.dataset.yardPage;


                    if (!page) {
                        return;
                    }


                    loadPage(
                        panel,
                        page
                    );
                }
            );


        /*
         * -----------------------------------------------------
         * MOBILE DETAIL (!)
         *
         * Hanya expand / collapse.
         * -----------------------------------------------------
         */

        $(panel)
            .off(
                `click${namespace}.mobileDetail`,
                '[data-yard-mobile-detail]'
            )
            .on(
                `click${namespace}.mobileDetail`,
                '[data-yard-mobile-detail]',
                function () {

                    toggleMobileDetail(
                        panel,
                        this
                    );
                }
            );


        /*
         * -----------------------------------------------------
         * PROSES DETAIL
         *
         * FLOW EXISTING TETAP.
         * -----------------------------------------------------
         */

        $(panel)
            .off(
                `click${namespace}`,
                '[data-detail-button]'
            )
            .on(
                `click${namespace}`,
                '[data-detail-button]',
                function () {

                    loadDetail(
                        panel,
                        this
                    );
                }
            );


        /*
         * -----------------------------------------------------
         * STORE
         * -----------------------------------------------------
         */

        $(panel)
            .off(
                `submit${namespace}`,
                '[data-store-form]'
            )
            .on(
                `submit${namespace}`,
                '[data-store-form]',
                function (event) {

                    event.preventDefault();


                    storeData(
                        panel,
                        this
                    );
                }
            );


        /*
         * -----------------------------------------------------
         * CLOSE DETAIL
         * -----------------------------------------------------
         */

        $(panel)
            .off(
                `click${namespace}`,
                '[data-close-detail]'
            )
            .on(
                `click${namespace}`,
                '[data-close-detail]',
                function () {

                    const detail =
                        panel.querySelector(
                            '[data-detail]'
                        );


                    if (!detail) {
                        return;
                    }


                    detail.classList.add(
                        'hidden'
                    );


                    detail.innerHTML =
                        '';


                    window.scrollTo({

                        top: 0,

                        behavior: 'smooth',
                    });
                }
            );


        /*
         * -----------------------------------------------------
         * LOAD INITIAL DATA
         * -----------------------------------------------------
         */

        loadInitialData(
            panel,
            1
        );
    }


    /*
     * =========================================================
     * INITIALIZE
     * =========================================================
     */

    function initMarshallingYard() {

        const panel =
            getPanel();


        if (!panel) {
            return;
        }


        /*
         * Panel baru setelah Livewire navigation
         * harus bisa dibind lagi.
         */
        panel.dataset.bound = '';


        bindPanel(
            panel
        );
    }


    /*
     * =========================================================
     * DOCUMENT READY
     * =========================================================
     */

    $(document).ready(
        function () {

            initMarshallingYard();
        }
    );


    /*
     * =========================================================
     * LIVEWIRE NAVIGATION
     * =========================================================
     */

    document.addEventListener(
        'livewire:navigated',
        function () {

            initMarshallingYard();
        }
    );


    /*
     * =========================================================
     * BOS RELOAD DATA
     * =========================================================
     */

    document.addEventListener(
        'bos:reload-data',
        function () {

            const panel =
                getPanel();


            if (panel) {

                loadInitialData(
                    panel,
                    1
                );
            }
        }
    );

})(jQuery);