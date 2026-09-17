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


    /**
     * SEARCH MONITORING REEFER
     *
     * Flow:
     *
     * Search
     *   ↓
     * Backend check monitoring aktif
     *   ↓
     * Ada
     *   ↓
     * Langsung render form
     */
    $(document).on(
        'submit',
        '[data-panel-name="monitoringreefer"] [data-search-form]',
        function (e) {
            e.preventDefault();

            const form = $(this);

            const button =
                form.find('[data-search-button]');

            const content =
                panel.find('[data-content]');

            const noCont = $.trim(
                form.find('[name="no_cont"]').val()
            );


            if (!noCont) {
                showAlert(
                    'Silakan masukkan No Container.'
                );

                return;
            }


            setButtonLoading(
                button,
                true
            );


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

                        content.html(
                            response.data
                        );

                    }

                },


                error: function (xhr) {

                    content.empty();


                    if (xhr.status === 404) {

                        const response =
                            xhr.responseJSON;

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


    /**
     * STORE / MONITORING
     *
     * Saat ini READ ONLY.
     * Tidak melakukan INSERT.
     */
    $(document).on(
        'submit',
        '[data-panel-name="monitoringreefer"] [data-store-form]',
        function (e) {

            e.preventDefault();


            const form = $(this);

            const button =
                form.find('[data-store-button]');


            const temperature = $.trim(
                form.find('[name="temperature"]').val()
            );


            if (!temperature) {

                showAlert(
                    'Temperature saat ini wajib diisi.'
                );

                return;
            }


            setButtonLoading(
                button,
                true
            );


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
                        'Data monitoring berhasil divalidasi.'
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
                            'Data monitoring tidak valid.'
                        );

                        return;
                    }


                    showAlert(
                        'Terjadi kesalahan saat memproses monitoring reefer.'
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