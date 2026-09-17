(function ($) {
    'use strict';

    function getPanel() {
        return $('[data-panel][data-panel-name="marshallingcic"]').first();
    }

    function showAlert(message, type = 'error') {
        if (typeof window.showAlert === 'function') {
            window.showAlert(message, type);
            return;
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type === 'success' ? 'success' : 'error',
                text: message,
            });
            return;
        }

        alert(message);
    }

    function setLoading($button, loading, text = 'Loading...') {
        if (typeof window.setButtonLoading === 'function') {
            window.setButtonLoading($button, loading, text);
            return;
        }

        if (!$button || !$button.length) {
            return;
        }

        if (loading) {
            $button.data('original-text', $button.text());
            $button.prop('disabled', true);
            $button.text(text);
        } else {
            $button.prop('disabled', false);
            $button.text($button.data('original-text') || 'Search');
        }
    }

    function loadInitialTable() {
        const $panel = getPanel();

        if (!$panel.length) {
            return;
        }

        const url = $panel.data('data-url');

        if (!url) {
            return;
        }

        const $container = $panel.find('[data-table-container]');

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',

            success: function (response) {
                if (response.data) {
                    $container.html(response.data);
                }
            },

            error: function (xhr) {
                const message =
                    xhr.responseJSON?.message ||
                    'Gagal mengambil data Marshalling CIC.';

                $container.html(`
                    <div class="rounded-2xl bg-white p-6 text-center
                                shadow-sm ring-1 ring-red-200
                                dark:bg-slate-900 dark:ring-red-900">
                        <p class="text-sm text-red-600 dark:text-red-400">
                            ${message}
                        </p>
                    </div>
                `);
            },
        });
    }

    function searchContainer(form) {
        const $panel = getPanel();

        if (!$panel.length) {
            return;
        }

        const url = $panel.data('search-url');

        if (!url) {
            return;
        }

        const $form = $(form);
        const $input = $form.find('[name="no_cont"]');
        const $button = $form.find('[data-search-button]');

        const noCont = $.trim($input.val() || '').toUpperCase();

        if (!noCont) {
            showAlert('Nomor Container wajib diisi.');
            $input.trigger('focus');
            return;
        }

        setLoading($button, true, 'Mencari...');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {
                no_cont: noCont,
                _token: $panel.data('csrf-token'),
            },

            success: function (response) {
                if (response.data) {
                    $panel
                        .find('[data-table-container]')
                        .html(response.data);

                    $panel
                        .find('[data-detail-container]')
                        .addClass('hidden')
                        .empty();
                }
            },

            error: function (xhr) {
                const message =
                    xhr.responseJSON?.message ||
                    'Data Container tidak ditemukan.';

                showAlert(message);
            },

            complete: function () {
                setLoading($button, false);
            },
        });
    }

    function loadDetail(idJobSlip, $button) {
        const $panel = getPanel();

        if (!$panel.length) {
            return;
        }

        const url = $panel.data('detail-url');

        if (!url) {
            return;
        }

        $button.prop('disabled', true);

        const originalText = $button.text();
        $button.text('Loading...');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {
                id_job_slip: idJobSlip,
                _token: $panel.data('csrf-token'),
            },

            success: function (response) {
                if (response.data) {
                    const $detail = $panel.find(
                        '[data-detail-container]'
                    );

                    $detail
                        .html(response.data)
                        .removeClass('hidden');

                    $('html, body').animate(
                        {
                            scrollTop: $detail.offset().top - 20,
                        },
                        300
                    );
                }
            },

            error: function (xhr) {
                const message =
                    xhr.responseJSON?.message ||
                    'Detail Marshalling CIC tidak ditemukan.';

                showAlert(message);
            },

            complete: function () {
                $button.prop('disabled', false);
                $button.text(originalText);
            },
        });
    }

    function resetSearch() {
        const $panel = getPanel();

        if (!$panel.length) {
            return;
        }

        $panel
            .find('[name="no_cont"]')
            .val('');

        $panel
            .find('[data-detail-container]')
            .addClass('hidden')
            .empty();

        loadInitialTable();
    }

    function bindEvents() {
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

        $(document)
            .off(
                'click.marshallingcic',
                '[data-panel-name="marshallingcic"] [data-detail-id]'
            )
            .on(
                'click.marshallingcic',
                '[data-panel-name="marshallingcic"] [data-detail-id]',
                function () {
                    const $button = $(this);
                    const idJobSlip = $button.data('detail-id');

                    if (!idJobSlip) {
                        return;
                    }

                    loadDetail(idJobSlip, $button);
                }
            );
    }

    $(document).on(
        'livewire:navigated.marshallingcic',
        function () {
            bindEvents();
            loadInitialTable();
        }
    );

    $(function () {
        bindEvents();
        loadInitialTable();
    });

})(jQuery);