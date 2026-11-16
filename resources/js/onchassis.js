$(function () {
    'use strict';

    const panel = $('[data-panel-name="onchassis"]');

    if (!panel.length) {
        return;
    }

    let searchRequest = null;

    function getCsrfToken() {
        return panel.attr('data-csrf-token') || '';
    }

    function getSearchUrl() {
        return panel.attr('data-search-url') || '';
    }

    function getStoreUrl() {
        return panel.attr('data-store-url') || '';
    }

    function showAlert(icon, title, message) {
        if (typeof window.showAlert === 'function') {
            window.showAlert(icon, title, message);
            return;
        }

        if (window.Swal?.fire) {
            window.Swal.fire({
                icon,
                title,
                text: message,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#0369a1',
            });
            return;
        }

        alert(message);
    }

    function showToast(icon, title, text = '') {
        if (typeof window.showToast === 'function') {
            window.showToast(icon, title, text);
            return;
        }

        if (window.Swal?.fire) {
            window.Swal.fire({
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

    function setButtonLoading(button, loading) {
        if (loading) {
            button.prop('disabled', true);

            if (!button.data('original-html')) {
                button.data('original-html', button.html());
            }

            button.html('Loading...');
        } else {
            button.prop('disabled', false);

            const originalHtml = button.data('original-html');

            if (originalHtml) {
                button.html(originalHtml);
            }
        }
    }

    $(document).on(
        'submit',
        '[data-panel-name="onchassis"] [data-search-form]',
        function (e) {
            e.preventDefault();

            const form = $(this);
            const button = form.find('[data-search-button]');
            const content = panel.find('[data-content]');

            const noCont = $.trim(
                form.find('[name="search_ch"]').val()
            );

            if (!noCont) {
                showAlert('info', 'Informasi', 'Silakan masukkan No Container.');
                return;
            }

            setButtonLoading(button, true);

            content.empty();

            searchRequest = $.ajax({
                url: getSearchUrl(),
                type: 'POST',
                dataType: 'json',

                data: {
                    search_ch: noCont,
                },

                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                },

                success: function (response) {
                    content.empty();

                    /**
                     * Tidak ditemukan.
                     */
                    if (response.status === 0) {
                        showAlert(
                            'info',
                            'Informasi',
                            response.message ||
                            `WARNING ! NO CONT : ${noCont} NOT FOUND`
                        );

                        return;
                    }

                    /**
                     * Langsung tampilkan form.
                     */
                    if (response.data) {
                        content.html(response.data);
                        showToast('info', 'Informasi', 'Container siap diproses On Chassis.');
                    }
                },

                error: function (xhr) {
                    content.empty();

                    if (xhr.statusText === 'abort') {
                        return;
                    }

                    if (xhr.status === 419) {
                        showAlert(
                            'warning',
                            'Sesi berakhir',
                            'Session/CSRF token sudah tidak valid. Silakan refresh halaman.'
                        );

                        return;
                    }

                    if ([404, 409, 422].includes(xhr.status)) {
                        showAlert(
                            'info',
                            'Informasi',
                            xhr.responseJSON?.message || 'Container belum memenuhi syarat On Chassis.'
                        );

                        return;
                    }

                    showAlert(
                        'error',
                        'Gagal',
                        'Terjadi kesalahan saat mencari container.'
                    );
                },

                complete: function () {
                    setButtonLoading(button, false);
                    searchRequest = null;
                },
            });
        }
    );

    $(document).on(
        'click',
        '[data-panel-name="onchassis"] [data-reset-button]',
        function () {
            const resetButton = $(this);
            const currentPanel = resetButton.closest('[data-panel-name="onchassis"]');
            const searchForm = currentPanel.find('[data-search-form]');
            const searchButton = searchForm.find('[data-search-button]');
            const searchInput = searchForm.find('[name="search_ch"]');

            if (searchRequest) {
                searchRequest.abort();
                searchRequest = null;
            }

            searchForm[0]?.reset();
            searchInput.trigger('input').trigger('change').trigger('focus');
            currentPanel.find('[data-content]').empty();
            setButtonLoading(searchButton, false);
        }
    );

    $(document).on(
        'submit',
        '[data-panel-name="onchassis"] [data-store-form]',
        function (e) {
            e.preventDefault();

            const form = $(this);
            const button = form.find('[data-store-button]');

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
                    if (response.status === 'success') {
                        showToast(
                            'success',
                            'Berhasil',
                            response.message || 'On Chassis berhasil diproses.'
                        );
                        panel.find('[data-content]').empty();
                        return;
                    }

                    showAlert('info', 'Informasi', response.message || 'Container belum dapat diproses.');
                },

                error: function (xhr) {
                    if (xhr.status === 419) {
                        showAlert(
                            'warning',
                            'Sesi berakhir',
                            'Session/CSRF token sudah tidak valid. Silakan refresh halaman.'
                        );

                        return;
                    }

                    if ([404, 409, 422].includes(xhr.status)) {
                        const response = xhr.responseJSON;

                        showAlert(
                            'info',
                            'Informasi',
                            response?.message ||
                            'Data belum memenuhi syarat On Chassis.'
                        );

                        return;
                    }

                    showAlert(
                        'error',
                        'Gagal',
                        'Terjadi kesalahan saat memproses On Chassis.'
                    );
                },

                complete: function () {
                    setButtonLoading(button, false);
                },
            });
        }
    );
});
