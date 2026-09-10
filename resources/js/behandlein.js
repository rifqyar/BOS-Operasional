(($) => {
    if (! $) {
        return;
    }

    /* ------------------------------------------------------------------ */
    /*  Behandle In panel elements & selectors                           */
    /* ------------------------------------------------------------------ */

    const getPanel = () => $('[data-panel][data-panel-name="behandlein"]').first();
    const hasPanel = () => getPanel().length > 0;

    const getNoContInput = () => $('#panel-no-container', getPanel());
    const getSearchForm = () => $('[data-search-form]', getPanel());
    const getSearchButton = () => $('[data-search-button]', getPanel());
    const getResultWrapper = () => $('[data-result]', getPanel());
    const getResultContainer = () => $('[data-result-container]', getPanel());
    const getRowsContainer = () => $('[data-rows]', getPanel());

    const getSearchUrl = () => getPanel().data('searchUrl');
    const getStoreUrl = () => getPanel().data('storeUrl');

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

    const initBehandleInPanel = () => {
        if (hasPanel()) {
            $('[data-handheld-menu]').addClass('hidden');
            getNoContInput().trigger('focus');
        }
    };

    /* ------------------------------------------------------------------ */
    /*  Bind event handlers                                               */
    /* ------------------------------------------------------------------ */

    const bindBehandleInHandlers = () => {
        /* ---- Reload data listener ---- */
        $(document).off('bos:reload-data.bosBehandleIn');
        $(document).on('bos:reload-data.bosBehandleIn', () => {
            const noCont = getNoContInput().val()?.trim();
            if (hasPanel() && noCont) {
                getSearchForm().trigger('submit');
            }
        });

        /* ---- Search No Container submit ---- */
        $(document).off('submit.bosBehandleInSearch', '[data-search-form]');
        $(document).on('submit.bosBehandleInSearch', '[data-search-form]', function (event) {
            if (! hasPanel()) return;

            event.preventDefault();

            const noCont = getNoContInput().val()?.trim();
            const $button = getSearchButton();
            const defaultLabel = $button.data('default-label') || $button.html();
            const searchUrl = getSearchUrl();

            $button.data('default-label', defaultLabel);

            if (! noCont) {
                showAlert('warning', 'Nomor Container kosong', 'Silakan isi nomor Container terlebih dahulu.');
                return;
            }

            if (! searchUrl) {
                showAlert('info', 'Dalam Pengembangan', 'Fitur pencarian Behandle In sedang dalam pengembangan.');
                return;
            }

            setButtonLoading($button, true, defaultLabel);

            $.ajax({
                url: searchUrl,
                method: 'POST',
                data: { no_cont: noCont },
                success: (response) => {
                    getResultContainer().text(noCont);
                    getRowsContainer().empty().html(response.data || '');
                    getResultWrapper().removeClass('hidden');
                },
                error: (xhr) => {
                    getResultWrapper().addClass('hidden');
                    let alertType = xhr.status >= '500' ? 'error' : 'info';
                    let alertTitle = xhr.status >= '500' ? 'Terjadi Kesalahan' : 'Container tidak ditemukan';
                    showAlert(alertType, alertTitle, xhr.responseJSON?.message || 'Gagal mengambil data container, harap hubungi tim IT');
                },
                complete: () => {
                    setButtonLoading($button, false, defaultLabel);
                },
            });
        });

        /* ---- Send / Store form submit ---- */
        $(document).off('submit.bosBehandleInSend', '[data-send-form]');
        $(document).on('submit.bosBehandleInSend', '[data-send-form]', function (event) {
            if (! hasPanel()) return;

            event.preventDefault();

            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            const defaultLabel = $button.data('default-label') || $button.html();
            const storeUrl = getStoreUrl();

            $button.data('default-label', defaultLabel);

            if (! storeUrl) {
                showAlert('info', 'Dalam Pengembangan', 'Fitur simpan Behandle In sedang dalam pengembangan.');
                return;
            }

            setButtonLoading($button, true, defaultLabel);

            $.ajax({
                url: storeUrl,
                method: 'POST',
                data: $form.serialize(),
                success: (response) => {
                    showAlert('success', 'Berhasil', response.message || 'Data Behandle In berhasil dikirim.');
                    $button
                        .text('Terkirim')
                        .removeClass('bg-emerald-600 hover:bg-emerald-700')
                        .addClass('bg-slate-500');
                },
                error: (xhr) => {
                    showAlert('error', 'Gagal', xhr.responseJSON?.message || 'Data Behandle In gagal dikirim.');
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
    bindBehandleInHandlers();
    initBehandleInPanel();

    $(() => initBehandleInPanel());
    $(document).on('livewire:navigated', () => {
        bindBehandleInHandlers();
        initBehandleInPanel();
    });
})(window.jQuery);
