(($) => {
    if (! $) {
        return;
    }

    /* ------------------------------------------------------------------ */
    /*  Pickup panel elements & selectors                                 */
    /* ------------------------------------------------------------------ */

    const getPanel = () => $('[data-panel][data-panel-name="pickup"], [data-pickup-panel]').first();
    const hasPanel = () => getPanel().length > 0;

    const getSpkInput = () => $('#panel-no-spk, #pickup-no-spk', getPanel());
    const getSearchForm = () => $('[data-search-form], [data-pickup-search-form]', getPanel());
    const getSearchButton = () => $('[data-search-button], [data-pickup-search-button]', getPanel());
    const getResultWrapper = () => $('[data-result], [data-pickup-result]', getPanel());
    const getResultSpk = () => $('[data-result-spk], [data-pickup-result-spk]', getPanel());
    const getRowsContainer = () => $('[data-rows], [data-pickup-rows]', getPanel());

    const getSearchUrl = () => getPanel().data('searchUrl') || getPanel().data('pickupSearchUrl');
    const getStoreUrl = () => getPanel().data('storeUrl') || getPanel().data('pickupStoreUrl');
    const getCsrfToken = () => {
        return getPanel().attr('data-csrf-token')
            || getPanel().data('csrfToken')
            || (typeof window.getCsrfToken === 'function' ? window.getCsrfToken() : '')
            || $('meta[name="csrf-token"]').attr('content')
            || '';
    };

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                           */
    /* ------------------------------------------------------------------ */

    const showAlert = (icon, title, text) => {
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
    };

    const setButtonLoading = ($button, isLoading, label) => {
        if (typeof window.setButtonLoading === 'function') {
            window.setButtonLoading($button, isLoading, label);
            return;
        }

        $button.prop('disabled', isLoading);
        $button.html(
            isLoading
                ? '<span class="inline-block size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Memproses'
                : label
        );
    };

    /* ------------------------------------------------------------------ */
    /*  Init panel state                                                  */
    /* ------------------------------------------------------------------ */

    const initPickupPanel = () => {
        if (hasPanel()) {
            $('[data-handheld-menu]').addClass('hidden');
            getSpkInput().trigger('focus');
        }
    };

    /* ------------------------------------------------------------------ */
    /*  Bind event handlers                                               */
    /* ------------------------------------------------------------------ */

    const bindPickupHandlers = () => {
        /* ---- Reload data listener ---- */
        $(document).off('bos:reload-data.bosPickup');
        $(document).on('bos:reload-data.bosPickup', () => {
            const noSpk = getSpkInput().val()?.trim();
            if (hasPanel() && noSpk) {
                getSearchForm().trigger('submit');
            }
        });

        /* ---- Search SPK submit ---- */
        $(document).off('submit.bosPickupSearch', '[data-search-form], [data-pickup-search-form]');
        $(document).on('submit.bosPickupSearch', '[data-search-form], [data-pickup-search-form]', function (event) {
            if (! hasPanel()) return;

            event.preventDefault();

            const noSpk = getSpkInput().val()?.trim();
            const $button = getSearchButton();
            const defaultLabel = $button.data('default-label') || $button.html();
            const searchUrl = getSearchUrl();

            $button.data('default-label', defaultLabel);

            if (! noSpk) {
                showAlert('warning', 'Nomor SPK kosong', 'Silakan isi nomor SPK terlebih dahulu.');
                return;
            }

            if (! searchUrl) {
                showAlert('error', 'Konfigurasi belum lengkap', 'URL pencarian pickup tidak ditemukan.');
                return;
            }

            setButtonLoading($button, true, defaultLabel);

            const token = getCsrfToken();

            $.ajax({
                url: searchUrl,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                },
                data: {
                    no_spk: noSpk,
                    _token: token,
                },
                success: (response) => {
                    getResultSpk().text(noSpk);
                    getRowsContainer().empty().html(response.data || '');
                    getResultWrapper().removeClass('hidden');
                },
                error: (xhr) => {
                    getResultWrapper().addClass('hidden');
                    showAlert('info', 'SPK tidak ditemukan', xhr.responseJSON?.message || 'Data SPK tidak ditemukan.');
                },
                complete: () => {
                    setButtonLoading($button, false, defaultLabel);
                },
            });
        });

        /* ---- Send / Store form submit ---- */
        $(document).off('submit.bosPickupSend', '[data-send-form], [data-pickup-send-form]');
        $(document).on('submit.bosPickupSend', '[data-send-form], [data-pickup-send-form]', function (event) {
            if (! hasPanel()) return;

            event.preventDefault();

            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            const defaultLabel = $button.data('default-label') || $button.html();
            const storeUrl = getStoreUrl();

            $button.data('default-label', defaultLabel);

            if (! storeUrl) {
                showAlert('error', 'Konfigurasi belum lengkap', 'URL simpan pickup tidak ditemukan.');
                return;
            }

            setButtonLoading($button, true, defaultLabel);

            const token = getCsrfToken();

            $.ajax({
                url: storeUrl,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                },
                data: $form.serialize(),
                success: (response) => {
                    showAlert('success', 'Berhasil', response.message || 'Data pickup berhasil dikirim.');
                    $button
                        .text('Terkirim')
                        .removeClass('bg-emerald-600 hover:bg-emerald-700')
                        .addClass('bg-slate-500');
                },
                error: (xhr) => {
                    showAlert('error', 'Gagal', xhr.responseJSON?.message || 'Data pickup gagal dikirim.');
                },
                complete: () => {
                    if ($button.text().trim() !== 'Terkirim') {
                        setButtonLoading($button, false, defaultLabel);
                    }
                },
            });
        });
    };

    // Execute
    bindPickupHandlers();
    initPickupPanel();

    $(() => initPickupPanel());
    $(document).on('livewire:navigated', () => {
        bindPickupHandlers();
        initPickupPanel();
    });
})(window.jQuery);
