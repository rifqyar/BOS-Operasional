$(function () {
    'use strict';

    const panel = $('[data-panel-name="stringstuffing"]');

    if (!panel.length) {
        return;
    }

    function getCsrfToken() {
        return panel.attr('data-csrf-token') || '';
    }

    function getSearchUrl() {
        return panel.attr('data-search-url') || '';
    }

    function getStoreUrl() {
        return panel.attr('data-store-url') || '';
    }

    function showAlert(message) {
        alert(message);
    }

    function setButtonLoading(button, loading) {
        if (loading) {
            button.prop('disabled', true);

            if (!button.data('original-html')) {
                button.data(
                    'original-html',
                    button.html()
                );
            }

            button.html('Loading...');
        } else {
            button.prop('disabled', false);

            const originalHtml =
                button.data('original-html');

            if (originalHtml) {
                button.html(originalHtml);
            }
        }
    }


    /*
     * SEARCH
     *
     * Search → langsung form
     */
    $(document).on(
        'submit',
        '[data-panel-name="stringstuffing"] [data-search-form]',
        function (e) {
            e.preventDefault();

            const form = $(this);

            const button =
                form.find('[data-search-button]');

            const content =
                panel.find('[data-content]');

            const noCont = $.trim(
                form.find('[name="search_cont"]').val()
            );

            if (!noCont) {
                showAlert(
                    'Silakan masukkan No Container.'
                );

                return;
            }

            setButtonLoading(button, true);

            content.empty();

            $.ajax({
                url: getSearchUrl(),

                type: 'POST',

                dataType: 'json',

                data: {
                    search_cont: noCont,
                },

                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                },

                success: function (response) {

                    content.empty();

                    if (!response.status) {
                        showAlert(
                            response.message ||
                            `NO CONT : ${noCont} NOT FOUND`
                        );

                        return;
                    }

                    if (response.data) {
                        content.html(response.data);
                    }
                },

                error: function (xhr) {

                    content.empty();

                    if (xhr.status === 404) {
                        const response =
                            xhr.responseJSON;

                        showAlert(
                            response?.message ||
                            `NO CONT : ${noCont} NOT FOUND`
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
                            'Nomor Container wajib diisi.'
                        );

                        return;
                    }

                    showAlert(
                        'Terjadi kesalahan saat mencari container.'
                    );
                },

                complete: function () {
                    setButtonLoading(
                        button,
                        false
                    );
                },
            });
        }
    );


    /*
     * STORE
     *
     * Read-only.
     */
    $(document).on(
        'submit',
        '[data-panel-name="stringstuffing"] [data-store-form]',
        function (e) {
            e.preventDefault();

            const form = $(this);

            const button =
                form.find('[data-store-button]');

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
                    showAlert(
                        response.message ||
                        'Proses belum diaktifkan.'
                    );
                },

                error: function (xhr) {

                    if (xhr.status === 419) {
                        showAlert(
                            'Session/CSRF token sudah tidak valid. Silakan refresh halaman.'
                        );

                        return;
                    }

                    if (xhr.status === 422) {
                        const response =
                            xhr.responseJSON;

                        showAlert(
                            response?.message ||
                            'String Stuffing masih dalam mode read-only.'
                        );

                        return;
                    }

                    showAlert(
                        'Terjadi kesalahan saat memproses String Stuffing.'
                    );
                },

                complete: function () {
                    setButtonLoading(
                        button,
                        false
                    );
                },
            });
        }
    );
});