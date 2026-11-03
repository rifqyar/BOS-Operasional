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

    /**
     * Store URL
     */
    function getStoreUrl() {
        return panel.attr('data-store-url') || '';
    }

    /**
     * Alert
     */
    function showAlert(message) {
        alert(message);
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
            const content = panel.find('[data-content]');

            searchForm[0]?.reset();

            searchInput.val('');
            content.empty();

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
            const content = panel.find('[data-content]');

            const noCont = $.trim(
                form.find('[name="no_cont"]').val()
            );

            if (!noCont) {
                showAlert('Silakan masukkan No Container.');
                return;
            }

            setButtonLoading(button, true);
            content.empty();

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
                    content.empty();

                    if (!response.status) {
                        showAlert(
                            response.message ||
                            `NO CONT : ${noCont} TIDAK DAPAT DIMONITOR`
                        );

                        return;
                    }

                    if (response.data) {
                        content.html(response.data);
                    } else {
                        showAlert(
                            response.message ||
                            'Data monitoring tidak tersedia.'
                        );
                    }
                },

                error: function (xhr) {
                    content.empty();

                    const response = xhr.responseJSON;

                    if (xhr.status === 404) {
                        showAlert(
                            response?.message ||
                            `NO CONT : ${noCont} SUDAH UNPLUGIN / TIDAK AKTIF MONITORING`
                        );

                        return;
                    }

                    if (xhr.status === 419) {
                        showAlert(
                            'Session/CSRF token sudah tidak valid. Silakan refresh halaman.'
                        );

                        return;
                    }

                    if (xhr.status === 422) {
                        showAlert(
                            response?.message ||
                            'Nomor Container wajib diisi.'
                        );

                        return;
                    }

                    showAlert(
                        response?.message ||
                        'Terjadi kesalahan saat mencari container.'
                    );
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
                showAlert('Nomor Container wajib diisi.');
                return;
            }

            if (!temperature) {
                showAlert('Temperature saat ini wajib diisi.');
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
                    if (!response.status) {
                        showAlert(
                            response.message ||
                            'Data monitoring gagal disimpan.'
                        );

                        return;
                    }

                    showAlert(
                        response.message ||
                        'Monitoring Reefer berhasil disimpan.'
                    );

                    /*
                     * Setelah berhasil, bersihkan form dan
                     * kembali ke pencarian container.
                     */
                    const searchForm = panel.find('[data-search-form]');
                    const searchInput = searchForm.find('[name="no_cont"]');
                    const content = panel.find('[data-content]');

                    searchForm[0]?.reset();
                    searchInput.val('');
                    content.empty();

                    searchInput.trigger('focus');
                },

                error: function (xhr) {
                    const response = xhr.responseJSON;

                    if (xhr.status === 419) {
                        showAlert(
                            'Session/CSRF token sudah tidak valid. Silakan refresh halaman.'
                        );

                        return;
                    }

                    if (xhr.status === 401) {
                        showAlert(
                            response?.message ||
                            'Session pengguna tidak ditemukan. Silakan login kembali.'
                        );

                        return;
                    }

                    if (xhr.status === 422) {
                        let message =
                            response?.message ||
                            'Data monitoring tidak valid.';

                        if (response?.data && typeof response.data === 'object') {
                            const errors = Object.values(response.data).flat();

                            if (errors.length) {
                                message = errors.join('\n');
                            }
                        }

                        showAlert(message);
                        return;
                    }

                    showAlert(
                        response?.message ||
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