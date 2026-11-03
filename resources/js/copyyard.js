$(function () {
    'use strict';

    const panelSelector = '[data-panel-name="copyyard"]';

    function getPanel() {
        return $(panelSelector);
    }

    function getCsrfToken(panel) {
        return panel.attr('data-csrf-token') || '';
    }

    function getSearchUrl(panel) {
        return panel.attr('data-search-url') || '';
    }

    function getStoreUrl(panel) {
        return panel.attr('data-store-url') || '';
    }

    function showAlert(message) {
        alert(message);
    }

    function setButtonLoading(button, loading, loadingText = 'Loading...') {
        if (!button || !button.length) {
            return;
        }

        if (loading) {
            button.prop('disabled', true);

            if (!button.data('original-html')) {
                button.data('original-html', button.html());
            }

            button.html(loadingText);
        } else {
            button.prop('disabled', false);

            const originalHtml = button.data('original-html');

            if (originalHtml) {
                button.html(originalHtml);
            }
        }
    }

    function clearMessage(panel) {
        panel.find('[data-message]')
            .addClass('hidden')
            .removeClass(
                'bg-red-100 text-red-700 ' +
                'bg-green-100 text-green-700 ' +
                'bg-amber-100 text-amber-700'
            )
            .text('');
    }

    function resetCopyYard(panel) {
        const searchForm = panel.find('[data-search-form]');

        if (searchForm.length) {
            searchForm[0].reset();
        }

        panel.find('[data-content]').empty();
        clearMessage(panel);

        panel.find('[name="search_cont"]').trigger('focus');
    }

    // =====================================================
    // SEARCH CONTAINER
    // =====================================================
    $(document)
        .off(
            'submit.copyyardSearch',
            `${panelSelector} [data-search-form]`
        )
        .on(
            'submit.copyyardSearch',
            `${panelSelector} [data-search-form]`,
            function (e) {
                e.preventDefault();

                const form = $(this);
                const panel = form.closest(panelSelector);
                const button = form.find('[data-search-button]');
                const content = panel.find('[data-content]');

                const noCont = $.trim(
                    form.find('[name="search_cont"]').val()
                );

                if (!noCont) {
                    showAlert('Silakan masukkan No Container.');
                    return;
                }

                clearMessage(panel);
                content.empty();

                setButtonLoading(button, true, 'Searching...');

                $.ajax({
                    url: getSearchUrl(panel),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        search_cont: noCont
                    },
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(panel)
                    }
                })
                .done(function (response) {
                    content.empty();

                    if (response.status === 0) {
                        showAlert(
                            response.message ||
                            `CONTAINER: ${noCont} NOT FOUND`
                        );
                        return;
                    }

                    if (response.status === 1 && response.data) {
                        content.html(response.data);
                        return;
                    }

                    showAlert(
                        response.message ||
                        'Data container tidak ditemukan.'
                    );
                })
                .fail(function (xhr) {
                    content.empty();

                    if (xhr.status === 419) {
                        showAlert(
                            'Session/CSRF token sudah tidak valid. Silakan refresh halaman.'
                        );
                        return;
                    }

                    if (xhr.status === 422) {
                        showAlert(
                            xhr.responseJSON?.message ||
                            'Nomor Container wajib diisi.'
                        );
                        return;
                    }

                    if (xhr.status === 404) {
                        showAlert(
                            xhr.responseJSON?.message ||
                            `CONTAINER: ${noCont} NOT FOUND`
                        );
                        return;
                    }

                    showAlert(
                        xhr.responseJSON?.message ||
                        'Terjadi kesalahan saat mencari container.'
                    );
                })
                .always(function () {
                    setButtonLoading(button, false);
                });
            }
        );

    // =====================================================
    // RESET
    // =====================================================
    $(document)
        .off(
            'click.copyyardReset',
            `${panelSelector} [data-reset-button]`
        )
        .on(
            'click.copyyardReset',
            `${panelSelector} [data-reset-button]`,
            function () {
                const panel = $(this).closest(panelSelector);
                resetCopyYard(panel);
            }
        );

    // =====================================================
    // CHANGE ACTION: BLOK LAPANGAN / BLOK SAMPAH
    // =====================================================
    $(document)
        .off(
            'change.copyyardAction',
            `${panelSelector} [data-action-block]`
        )
        .on(
            'change.copyyardAction',
            `${panelSelector} [data-action-block]`,
            function () {
                const action = $(this).val();
                const form = $(this).closest('[data-store-form]');
                const locationInput = form.find('[data-new-location]');

                if (!locationInput.length) {
                    return;
                }

                if (action === 'dua') {
                    locationInput
                        .val('SAMPAH')
                        .prop('readonly', true)
                        .prop('required', false);
                } else {
                    locationInput
                        .val('')
                        .prop('readonly', false)
                        .prop('required', true);
                }
            }
        );

    // =====================================================
    // STORE COPY YARD
    // =====================================================
    $(document)
        .off(
            'submit.copyyardStore',
            `${panelSelector} [data-store-form]`
        )
        .on(
            'submit.copyyardStore',
            `${panelSelector} [data-store-form]`,
            function (e) {
                e.preventDefault();

                const form = $(this);
                const panel = form.closest(panelSelector);
                const button = form.find('[data-store-button]');

                const noCont = $.trim(
                    form.find('[name="nomerkon"]').val()
                );

                const actionBlock = form.find('[name="mySelect"]').val();

                const locationInput = form.find('[name="lokbar"]');
                let newLocation = $.trim(locationInput.val());

                if (!noCont) {
                    showAlert('Nomor container tidak ditemukan pada form.');
                    return;
                }

                if (actionBlock === 'dua') {
                    newLocation = 'SAMPAH';
                    locationInput.val('SAMPAH');
                } else if (!newLocation) {
                    showAlert('Lokasi baru wajib diisi.');
                    locationInput.trigger('focus');
                    return;
                }

                // Payload eksplisit: input disabled tidak ikut serialize().
                const payload = {
                    nomerkon: noCont,
                    mySelect: actionBlock
                };

                if (actionBlock === 'satu') {
                    payload.lokbar = newLocation;
                }

                setButtonLoading(button, true, 'Menyimpan...');

                $.ajax({
                    url: getStoreUrl(panel),
                    type: 'POST',
                    dataType: 'json',
                    data: payload,
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(panel)
                    }
                })
                .done(function (response) {
                    if (response.status === 'success') {
                        showAlert(
                            response.message ||
                            'Copy Yard berhasil disimpan.'
                        );

                        resetCopyYard(panel);
                        return;
                    }

                    showAlert(
                        response.message ||
                        'Proses Copy Yard gagal.'
                    );
                })
                .fail(function (xhr) {
                    if (xhr.status === 419) {
                        showAlert(
                            'Session/CSRF token sudah tidak valid. Silakan refresh halaman.'
                        );
                        return;
                    }

                    if (xhr.status === 422) {
                        showAlert(
                            xhr.responseJSON?.message ||
                            'Data Copy Yard tidak valid.'
                        );
                        return;
                    }

                    if (xhr.status === 404) {
                        showAlert(
                            xhr.responseJSON?.message ||
                            'Container tidak ditemukan atau statusnya sudah berubah.'
                        );
                        return;
                    }

                    showAlert(
                        xhr.responseJSON?.message ||
                        'Terjadi kesalahan saat memproses Copy Yard.'
                    );
                })
                .always(function () {
                    setButtonLoading(button, false);
                });
            }
        );

});