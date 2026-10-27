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

    function showAlert(type, title, message, callback = null) {
        const allowedTypes = [
            'success',
            'error',
            'info',
            'warning',
            'question'
        ];

        // Mendukung pemanggilan lama:
        // showAlert('Pesan')
        // showAlert('Pesan', 'error')
        if (
            arguments.length <= 2 &&
            !allowedTypes.includes(String(type).toLowerCase())
        ) {
            const oldMessage = type;

            const oldType =
                typeof title === 'string' &&
                allowedTypes.includes(title.toLowerCase())
                    ? title.toLowerCase()
                    : 'error';

            type = oldType;

            title =
                oldType === 'success' ? 'Berhasil'
                : oldType === 'info' ? 'Informasi'
                : oldType === 'warning' ? 'Peringatan'
                : oldType === 'question' ? 'Konfirmasi'
                : 'Terjadi Kesalahan';

            message = oldMessage;
        }

        type = String(type || 'error').toLowerCase();

        if (!allowedTypes.includes(type)) {
            type = 'error';
        }

        if (!title) {
            title =
                type === 'success' ? 'Berhasil'
                : type === 'info' ? 'Informasi'
                : type === 'warning' ? 'Peringatan'
                : type === 'question' ? 'Konfirmasi'
                : 'Terjadi Kesalahan';
        }

        message = message || '';

        /*
         * Gunakan helper alert global apabila tersedia.
         *
         * Catatan:
         * Helper global harus menerima parameter:
         * showAlert(type, title, message, callback)
         */
        if (
            typeof window.showAlert === 'function' &&
            window.showAlert !== showAlert
        ) {
            window.showAlert(type, title, message, callback);
            return;
        }

        // Fallback menggunakan SweetAlert2.
        if (
            typeof window.Swal !== 'undefined' &&
            typeof window.Swal.fire === 'function'
        ) {
            window.Swal.fire({
                icon: type,
                title: title,
                text: message,
                confirmButtonText: 'OK'
            }).then(function () {
                if (typeof callback === 'function') {
                    callback();
                }
            });

            return;
        }

        // Fallback terakhir apabila SweetAlert2 tidak tersedia.
        window.alert(title + '\n\n' + message);

        if (typeof callback === 'function') {
            callback();
        }
    }

    /*
     * =========================================================
     * BUTTON LOADING
     * =========================================================
     */

    function setLoading($button, loading, text = 'Loading...') {
        if (!$button || !$button.length) {
            return;
        }

        let originalText = $button.data(
            'marshallingcic-original-text'
        );

        if (!originalText) {
            if ($button.is('[data-search-button]')) {
                originalText = 'Search';
            } else if ($button.is('[data-reset-button]')) {
                originalText = 'Reset';
            } else {
                originalText = $button.text().trim();
            }

            if (
                [
                    'Loading...',
                    'Mencari...',
                    'Memuat data...'
                ].includes(originalText)
            ) {
                originalText = $button.is('[data-reset-button]')
                    ? 'Reset'
                    : 'Search';
            }

            $button.data(
                'marshallingcic-original-text',
                originalText
            );
        }

        $button.prop('disabled', Boolean(loading));

        $button.text(
            loading ? text : originalText
        );
    }

    /*
     * =========================================================
     * LOAD INITIAL TABLE / PAGINATION
     * =========================================================
     */

    function loadInitialTable(page = 1, onComplete = null) {
        const $panel = getPanel();

        if (!$panel.length) {
            if (typeof onComplete === 'function') {
                onComplete();
            }

            return;
        }

        const url = $panel.data('data-url');

        const $container = $panel.find(
            '[data-table-container]'
        );

        if (!url) {
            if (typeof onComplete === 'function') {
                onComplete();
            }

            showAlert(
                'error',
                'Konfigurasi Belum Lengkap',
                'URL data Marshalling CIC tidak ditemukan.'
            );

            return;
        }

        const perPage =
            parseInt($panel.data('per-page'), 10) || 10;

        // Tampilkan loading pada area tabel.
        $container.html(`
            <div class="rounded-2xl bg-white p-6 text-center shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-700">
                <div class="flex items-center justify-center gap-3">
                    <svg
                        class="h-5 w-5 animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                        ></path>
                    </svg>

                    <span class="text-sm text-slate-600 dark:text-slate-300">
                        Memuat data Marshalling CIC...
                    </span>
                </div>
            </div>
        `);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',

            data: {
                page: page,
                per_page: perPage
            },

            success: function (response) {
                if (
                    response &&
                    typeof response.data === 'string'
                ) {
                    $container.html(response.data);
                } else {
                    $container.html(`
                        <div class="rounded-xl bg-amber-50 p-4 text-center text-sm text-amber-700">
                            Format respons data Marshalling CIC tidak sesuai.
                        </div>
                    `);

                    showAlert(
                        'error',
                        'Format Respons Tidak Sesuai',
                        'Data tabel tidak dikembalikan dalam format HTML yang diharapkan.'
                    );
                }
            },

            error: function (xhr) {
                const message =
                    xhr.responseJSON?.message ||
                    'Gagal mengambil data Marshalling CIC.';

                $container.html(`
                    <div class="rounded-2xl bg-white p-6 text-center shadow-sm ring-1 ring-red-200 dark:bg-slate-900 dark:ring-red-900">
                        <p class="text-sm text-red-600 dark:text-red-400"></p>
                    </div>
                `);

                $container.find('p').text(message);

                showAlert(
                    'error',
                    'Gagal Memuat Data',
                    message
                );
            },

            complete: function () {
                if (typeof onComplete === 'function') {
                    onComplete();
                }
            }
        });
    }

    /*
     * =========================================================
     * LOAD PAGE / PAGINATION
     * =========================================================
     */

    function loadPage(page) {
        const pageNumber = parseInt(page, 10);

        if (!pageNumber || pageNumber < 1) {
            return;
        }

        loadInitialTable(pageNumber);

        // Scroll kembali ke area tabel.
        const $panel = getPanel();

        if (!$panel.length) {
            return;
        }

        const $container = $panel.find(
            '[data-table-container]'
        );

        if ($container.length) {
            $('html, body').animate(
                {
                    scrollTop: $container.offset().top - 20
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
        const $panel = getPanel();

        if (!$panel.length) {
            return;
        }

        const url = $panel.data('search-url');

        if (!url) {
            showAlert(
                'error',
                'Konfigurasi Belum Lengkap',
                'URL pencarian Marshalling CIC tidak ditemukan.'
            );

            return;
        }

        const $form = $(form);

        const $input = $form.find(
            '[name="no_cont"]'
        );

        const $button = $form.find(
            '[data-search-button]'
        );

        const noCont = $.trim(
            $input.val() || ''
        ).toUpperCase();

        // Validasi nomor container.
        if (!noCont) {
            showAlert(
                'warning',
                'Nomor Container Kosong',
                'Nomor Container wajib diisi.'
            );

            $input.trigger('focus');

            return;
        }

        // Cegah request berulang saat masih loading.
        if ($button.prop('disabled')) {
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
                no_cont: noCont,
                _token: $panel.data('csrf-token')
            },

            success: function (response) {
                if (
                    response &&
                    typeof response.data === 'string'
                ) {
                    $panel.find(
                        '[data-table-container]'
                    ).html(response.data);

                    $panel.find(
                        '[data-detail-container]'
                    )
                    .addClass('hidden')
                    .empty();

                    // Jika backend mengirim success: false,
                    // jangan tampilkan hasil sebagai sukses.
                    if (response.success === false) {
                        showAlert(
                            'warning',
                            'Pencarian',
                            response.message ||
                                'Data Container tidak ditemukan.'
                        );
                    }
                } else {
                    showAlert(
                        'error',
                        'Respons Tidak Sesuai',
                        response?.message ||
                            'Respons pencarian tidak memiliki data yang sesuai.'
                    );
                }
            },

            error: function (xhr) {
                const message =
                    xhr.responseJSON?.message ||
                    xhr.responseJSON?.error ||
                    (
                        xhr.status === 404
                            ? 'Data Container tidak ditemukan.'
                            : xhr.status === 419
                                ? 'Sesi telah berakhir. Silakan muat ulang halaman.'
                                : 'Terjadi kesalahan saat mencari Container.'
                    );

                showAlert(
                    'error',
                    'Pencarian Gagal',
                    message
                );
            },

            complete: function () {
                // Tombol Search harus kembali normal
                // baik request berhasil maupun gagal.
                setLoading(
                    $button,
                    false
                );
            }
        });
    }

    /*
     * =========================================================
     * LOAD DETAIL / PROSES MARSHALLING
     *
     * FLOW LAMA TETAP DIPERTAHANKAN.
     * =========================================================
     */

    function loadDetail(idJobSlip, $button) {
        const $panel = getPanel();

        if (!$panel.length) {
            return;
        }

        const url = $panel.data('detail-url');

        if (!url) {
            showAlert(
                'error',
                'Konfigurasi Belum Lengkap',
                'URL detail Marshalling CIC tidak ditemukan.'
            );

            return;
        }

        // Cegah klik berulang saat detail sedang dimuat.
        if ($button.prop('disabled')) {
            return;
        }

        const originalText = $button.text().trim();

        $button.prop('disabled', true);
        $button.text('Loading...');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',

            data: {
                id_job_slip: idJobSlip,
                _token: $panel.data('csrf-token')
            },

            success: function (response) {
                if (
                    response &&
                    typeof response.data === 'string'
                ) {
                    const $detail = $panel.find(
                        '[data-detail-container]'
                    );

                    $detail
                        .html(response.data)
                        .removeClass('hidden');

                    $('html, body').animate(
                        {
                            scrollTop: $detail.offset().top - 20
                        },
                        300
                    );
                } else {
                    showAlert(
                        'error',
                        'Detail Tidak Tersedia',
                        response?.message ||
                            'Data detail tidak ditemukan atau format respons tidak sesuai.'
                    );
                }
            },

            error: function (xhr) {
                const message =
                    xhr.responseJSON?.message ||
                    xhr.responseJSON?.error ||
                    'Detail Marshalling CIC tidak ditemukan.';

                showAlert(
                    'error',
                    'Gagal Memuat Detail',
                    message
                );
            },

            complete: function () {
                // Pulihkan tombol setelah request selesai.
                $button.prop('disabled', false);
                $button.text(originalText);
            }
        });
    }

    /*
     * =========================================================
     * MOBILE DETAIL (!)
     *
     * Hanya membuka atau menutup detail baris.
     * Tidak memanggil loadDetail().
     * Tidak menjalankan proses Marshalling.
     * =========================================================
     */

    function toggleMobileDetail(button) {
        const $button = $(button);

        const number = $button.data(
            'cic-mobile-detail'
        );

        if (
            number === undefined ||
            number === null
        ) {
            return;
        }

        const $panel = getPanel();

        if (!$panel.length) {
            return;
        }

        const $detail = $panel.find(
            `[data-cic-mobile-detail-row="${number}"]`
        );

        if (!$detail.length) {
            return;
        }

        const isHidden = $detail.hasClass('hidden');

        if (isHidden) {
            // Buka detail.
            $detail.removeClass('hidden');

            $button.attr(
                'aria-expanded',
                'true'
            );

            $button.addClass(
                'bg-amber-100',
                'dark:bg-amber-400/10'
            );
        } else {
            // Tutup detail.
            $detail.addClass('hidden');

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

    function resetSearch(button) {
        const $panel = getPanel();

        if (!$panel.length) {
            return;
        }

        const $resetButton = $(button);

        // Jangan jalankan Reset berulang kali.
        if ($resetButton.prop('disabled')) {
            return;
        }

        // Kosongkan input pencarian.
        $panel.find('[name="no_cont"]').val('');

        // Sembunyikan dan kosongkan detail.
        $panel.find('[data-detail-container]')
            .addClass('hidden')
            .empty();

        // Tampilkan loading pada tombol Reset.
        setLoading(
            $resetButton,
            true,
            'Memuat data...'
        );

        // Muat kembali tabel dari halaman pertama.
        loadInitialTable(1, function () {
            // Pulihkan tombol setelah request selesai.
            setLoading(
                $resetButton,
                false
            );
        });
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

                    searchContainer(this);
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
                function (event) {
                    event.preventDefault();

                    resetSearch(this);
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

                    const page = $(this).data(
                        'cic-page'
                    );

                    if (!page) {
                        return;
                    }

                    loadPage(page);
                }
            );

        /*
         * -----------------------------------------------------
         * MOBILE DETAIL (!)
         *
         * Handler ini harus terpisah dari tombol PROSES.
         * Tombol ! hanya membuka atau menutup detail baris.
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
                function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    toggleMobileDetail(this);
                }
            );

        /*
         * -----------------------------------------------------
         * PROSES MARSHALLING CIC
         *
         * Flow tombol PROSES tetap dipertahankan.
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
                function (event) {
                    event.preventDefault();

                    const $button = $(this);

                    const idJobSlip = $button.data(
                        'detail-id'
                    );

                    if (!idJobSlip) {
                        showAlert(
                            'warning',
                            'Data Tidak Lengkap',
                            'ID Job Slip tidak ditemukan.'
                        );

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
     *
     * Bind ulang event dan muat tabel setelah navigasi Livewire.
     * =========================================================
     */

    $(document)
        .off('livewire:navigated.marshallingcic')
        .on(
            'livewire:navigated.marshallingcic',
            function () {
                bindEvents();

                loadInitialTable(1);
            }
        );

    /*
     * =========================================================
     * INITIAL PAGE LOAD
     * =========================================================
     */

    $(function () {
        bindEvents();

        loadInitialTable(1);
    });

})(jQuery);