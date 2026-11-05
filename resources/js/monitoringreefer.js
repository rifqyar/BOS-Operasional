$(function () {
    'use strict';

    const panel = $('[data-panel-name="monitoringreefer"]');

    if (!panel.length) {
        return;
    }

    /**
     * CSRF Token
     */
    function getCsrfToken() {
        return panel.attr('data-csrf-token') || '';
    }

    /**
     * Search URL
     */
    function getSearchUrl() {
        return panel.attr('data-search-url') || '';
    }

    function getDetailUrl() {
        return panel.attr('data-detail-url') || '';
    }

    /**
     * Store URL
     */
    function getStoreUrl() {
        return panel.attr('data-store-url') || '';
    }

    /**
     * Alert
     */
    function showAlert(icon, title, text) {
        if (typeof window.showAlert === 'function') {
            window.showAlert(icon, title, text);
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
    }

    function showToast(icon, title, text = '') {
        if (typeof window.showToast === 'function') {
            window.showToast(icon, title, text);
            return;
        }

        if (window.Swal) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon,
                title: title || text,
                text: title ? text : '',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            return;
        }

        alert(text || title);
    }

    function getAjaxErrorMessage(xhr, fallback) {
        const response = xhr.responseJSON || {};
        const firstValidationError = Object.values(response.errors || {})
            .flat()
            .find((message) => typeof message === 'string' && message.trim());

        return firstValidationError || response.message || fallback;
    }

    function showRequestError(xhr, fallback) {
        if (xhr.status === 419) {
            showAlert(
                'warning',
                'Sesi Tidak Valid',
                'CSRF token tidak valid atau sesi telah berubah. Silakan refresh halaman.'
            );
            return;
        }

        const isInfo = [401, 404, 409, 422].includes(xhr.status);
        showAlert(
            isInfo ? 'info' : 'error',
            xhr.status === 404
                ? 'Container tidak ditemukan'
                : xhr.status === 401
                    ? 'Sesi pengguna tidak ditemukan'
                    : xhr.status === 409
                        ? 'Status container berubah'
                        : xhr.status === 422
                            ? 'Data tidak valid'
                            : 'Terjadi Kesalahan',
            getAjaxErrorMessage(xhr, fallback)
        );
    }

    /**
     * Button loading
     */
    function setButtonLoading(button, loading) {
        if (!button || !button.length) {
            return;
        }

        if (loading) {
            if (!button.data('original-html')) {
                button.data('original-html', button.html());
            }

            button.prop('disabled', true);
            button.html('Loading...');
        } else {
            button.prop('disabled', false);

            const originalHtml = button.data('original-html');

            if (originalHtml) {
                button.html(originalHtml);
                button.removeData('original-html');
            }
        }
    }

    /**
     * RESET FORM SEARCH
     *
     * - Kosongkan input No Container
     * - Bersihkan hasil pencarian/form monitoring
     * - Fokus kembali ke input
     */
    $(document).on(
        'click',
        '[data-panel-name="monitoringreefer"] [data-reset-button]',
        function (e) {
            e.preventDefault();

            const searchForm = panel.find('[data-search-form]');
            const searchInput = searchForm.find('[name="no_cont"]');
            const table = panel.find('[data-table]');
            const detail = panel.find('[data-detail]');

            searchForm[0]?.reset();

            searchInput.val('');
            table.empty();
            detail.empty();

            searchInput.trigger('focus');
        }
    );

    /**
     * SEARCH MONITORING REEFER
     *
     * Flow:
     * Search
     *   ↓
     * Backend check monitoring aktif
     *   ↓
     * Jika ditemukan, render form monitoring
     */
    $(document).on(
        'submit',
        '[data-panel-name="monitoringreefer"] [data-search-form]',
        function (e) {
            e.preventDefault();

            const form = $(this);
            const button = form.find('[data-search-button]');
            const table = panel.find('[data-table]');
            const detail = panel.find('[data-detail]');

            const noCont = $.trim(
                form.find('[name="no_cont"]').val()
            );

            if (!noCont) {
                showAlert(
                    'info',
                    'No Container kosong',
                    'Silakan masukkan No Container.'
                );
                return;
            }

            setButtonLoading(button, true);
            table.empty();
            detail.empty();

            $.ajax({
                url: getSearchUrl(),
                type: 'POST',
                dataType: 'json',

                data: {
                    no_cont: noCont,
                },

                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                },

                success: function (response) {
                    table.empty();
                    detail.empty();

                    if (response.success !== true) {
                        showAlert(
                            'info',
                            'Monitoring tidak tersedia',
                            response.message ||
                                `NO CONT : ${noCont} TIDAK DAPAT DIMONITOR`
                        );

                        return;
                    }

                    if (response.data) {
                        table.html(response.data);
                    } else {
                        showAlert(
                            'info',
                            'Data tidak tersedia',
                            response.message || 'Data monitoring tidak tersedia.'
                        );
                    }
                },

                error: function (xhr) {
                    table.empty();
                    detail.empty();

                    showRequestError(
                        xhr,
                        `NO CONT : ${noCont} TIDAK AKTIF MONITORING`
                    );
                },

                complete: function () {
                    setButtonLoading(button, false);
                },
            });
        }
    );

    /** Ambil form untuk container yang dipilih dari daftar pencarian. */
    $(document).on(
        'click',
        '[data-panel-name="monitoringreefer"] [data-detail-button]',
        function (e) {
            e.preventDefault();

            const button = $(this);
            const noCont = String(button.data('no-cont') || '').trim();
            const detail = panel.find('[data-detail]');
            const url = getDetailUrl();

            if (!noCont) {
                showAlert('info', 'No Container kosong', 'No Container tidak ditemukan.');
                return;
            }

            if (!url) {
                showAlert('error', 'Konfigurasi belum lengkap', 'URL detail Monitoring Reefer tidak ditemukan.');
                return;
            }

            setButtonLoading(button, true);
            detail.empty();

            $.ajax({
                url,
                type: 'POST',
                dataType: 'json',
                data: { no_cont: noCont },
                headers: { 'X-CSRF-TOKEN': getCsrfToken() },
                success: function (response) {
                    if (response.success !== true || !response.data) {
                        showAlert(
                            'info',
                            'Monitoring tidak tersedia',
                            response.message || 'Data monitoring tidak tersedia.'
                        );
                        return;
                    }

                    detail.html(response.data);
                    detail[0]?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                },
                error: function (xhr) {
                    showRequestError(xhr, 'Gagal mengambil detail Monitoring Reefer.');
                },
                complete: function () {
                    setButtonLoading(button, false);
                },
            });
        }
    );

    /**
     * STORE / MONITORING REEFER
     *
     * Mengirim data ke controller untuk divalidasi,
     * lalu disimpan ke Oracle melalui service.
     */
    $(document).on(
        'submit',
        '[data-panel-name="monitoringreefer"] [data-store-form]',
        function (e) {
            e.preventDefault();

            const form = $(this);
            const button = form.find('[data-store-button]');

            const noCont = $.trim(
                form.find('[name="nomerkon"]').val()
            );

            const temperature = $.trim(
                form.find('[name="temperature"]').val()
            );

            if (!noCont) {
                showAlert(
                    'info',
                    'No Container kosong',
                    'Nomor Container wajib diisi.'
                );
                return;
            }

            if (!temperature) {
                showAlert(
                    'info',
                    'Temperature kosong',
                    'Temperature saat ini wajib diisi.'
                );
                return;
            }

            setButtonLoading(button, true);

            $.ajax({
                url: getStoreUrl(),
                type: 'POST',
                dataType: 'json',

                data: form.serialize(),

                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                },

                success: function (response) {
                    if (response.success !== true) {
                        showAlert(
                            'info',
                            'Data tidak dapat disimpan',
                            response.message || 'Data monitoring gagal disimpan.'
                        );

                        return;
                    }

                    showToast(
                        'success',
                        'Berhasil',
                        response.message ||
                        'Monitoring Reefer berhasil disimpan.'
                    );

                    /*
                     * Setelah berhasil, bersihkan form dan
                     * kembali ke pencarian container.
                     */
                    const searchForm = panel.find('[data-search-form]');
                    const searchInput = searchForm.find('[name="no_cont"]');
                    const table = panel.find('[data-table]');
                    const detail = panel.find('[data-detail]');

                    searchForm[0]?.reset();
                    searchInput.val('');
                    table.empty();
                    detail.empty();

                    searchInput.trigger('focus');
                },

                error: function (xhr) {
                    showRequestError(
                        xhr,
                        'Terjadi kesalahan saat menyimpan Monitoring Reefer.'
                    );
                },

                complete: function () {
                    setButtonLoading(button, false);
                },
            });
        }
    );
});
