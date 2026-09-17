$(function () {
    'use strict';

    const panel = $('[data-panel-name="copyyard"]');

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


    $(document).on(
        'submit',
        '[data-panel-name="copyyard"] [data-search-form]',
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

                    if (response.status === 0) {

                        showAlert(
                            response.message ||
                            `KONTAINER : ${noCont} NOT FOUND`
                        );

                        return;
                    }

                    if (response.data) {

                        content.html(
                            response.data
                        );

                    }

                },

                error: function (xhr) {

                    content.empty();

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


    $(document).on(
        'change',
        '[data-panel-name="copyyard"] [data-action-block]',
        function () {

            const action =
                $(this).val();

            const locationInput =
                panel.find('[data-new-location]');

            if (!locationInput.length) {
                return;
            }

            if (action === 'dua') {

                locationInput
                    .val('SAMPAH')
                    .prop('disabled', true);

            } else {

                locationInput
                    .val('')
                    .prop('disabled', false);

            }

        }
    );


    $(document).on(
        'submit',
        '[data-panel-name="copyyard"] [data-store-form]',
        function (e) {

            e.preventDefault();

            const form = $(this);

            const button =
                form.find('[data-store-button]');

            const actionBlock =
                form.find('[name="mySelect"]').val();

            const newLocation =
                $.trim(
                    form.find('[name="lokbar"]').val()
                );

            if (actionBlock === 'dua') {

                form.find('[name="lokbar"]')
                    .val('SAMPAH');

            } else if (!newLocation) {

                showAlert(
                    'Lokasi baru wajib diisi.'
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

                    showAlert(
                        response.message ||
                        'Copy Yard belum diaktifkan.'
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
                            'Copy Yard masih dalam mode read-only.'
                        );

                        return;
                    }

                    showAlert(
                        'Terjadi kesalahan saat memproses Copy Yard.'
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