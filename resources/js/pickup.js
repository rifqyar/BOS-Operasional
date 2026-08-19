($ => {
    if (! $) {
        return;
    }

    const getPickupPanel = () => $('[data-pickup-panel]').first();

    const showAlert = (icon, title, text) => {
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
        $button.prop('disabled', isLoading);
        $button.html(isLoading
            ? '<span class="inline-block size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Memproses'
            : label
        );
    };

    const initPickupPanel = () => {
        if (getPickupPanel().length) {
            $('[data-handheld-menu]').addClass('hidden');
            $('#pickup-no-spk').trigger('focus');
        }
    };

    const getPanelUrl = key => getPickupPanel().data(key);

    const renderPickupRows = view => {
        $('[data-pickup-rows]').empty().html(view);
    };

    const bindAjaxDefaults = () => {
        $.ajaxSetup({
            beforeSend: xhr => {
                const token = getPickupPanel().data('csrfToken') || $('meta[name="csrf-token"]').attr('content');

                if (token) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }
            },
        });
    };

    const bindHandlers = () => {
        bindAjaxDefaults();

        $(document).off('click.bosReloadHandheld', '[data-reload-handheld]');
        $(document).on('click.bosReloadHandheld', '[data-reload-handheld]', function () {
            const $button = $(this);
            const originalHtml = $button.html();

            $button.prop('disabled', true);
            $button.html('<span class="inline-block size-5 animate-spin rounded-full border-2 border-slate-400/40 border-t-sky-600 dark:border-white/30 dark:border-t-white"></span>');
            $(document).trigger('bos:reload-data');

            setTimeout(() => {
                $button.prop('disabled', false);
                $button.html(originalHtml);
            }, 800);
        });

        $(document).off('bos:reload-data.bosPickup');
        $(document).on('bos:reload-data.bosPickup', () => {
            const noSpk = $('#pickup-no-spk').val()?.trim();

            if ($('[data-pickup-panel]').is(':visible') && noSpk) {
                $('[data-pickup-search-form]').trigger('submit');
            }
        });

        $(document).off('submit.bosPickupSearch', '[data-pickup-search-form]');
        $(document).on('submit.bosPickupSearch', '[data-pickup-search-form]', function (event) {
            event.preventDefault();

            const noSpk = $('#pickup-no-spk').val().trim();
            const $button = $('[data-pickup-search-button]');
            const defaultLabel = $button.data('default-label') || $button.html();
            const searchUrl = getPanelUrl('pickupSearchUrl');

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

            $.ajax({
                url: searchUrl,
                method: 'POST',
                data: {
                    no_spk: noSpk,
                },
                success: response => {
                    $('[data-pickup-result-spk]').text(noSpk);
                    renderPickupRows(response.data || '');
                    $('[data-pickup-result]').removeClass('hidden');
                },
                error: xhr => {
                    $('[data-pickup-result]').addClass('hidden');
                    showAlert('info', 'SPK tidak ditemukan', xhr.responseJSON?.message || 'Data SPK tidak ditemukan.');
                },
                complete: () => {
                    setButtonLoading($button, false, defaultLabel);
                },
            });
        });

        $(document).off('submit.bosPickupSend', '[data-pickup-send-form]');
        $(document).on('submit.bosPickupSend', '[data-pickup-send-form]', function (event) {
            event.preventDefault();

            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            const defaultLabel = $button.data('default-label') || $button.html();
            const storeUrl = getPanelUrl('pickupStoreUrl');

            $button.data('default-label', defaultLabel);

            if (! storeUrl) {
                showAlert('error', 'Konfigurasi belum lengkap', 'URL simpan pickup tidak ditemukan.');
                return;
            }

            setButtonLoading($button, true, defaultLabel);

            $.ajax({
                url: storeUrl,
                method: 'POST',
                data: $form.serialize(),
                success: response => {
                    showAlert('success', 'Berhasil', response.message || 'Data pickup berhasil dikirim.');
                    $button.text('Terkirim')
                        .removeClass('bg-emerald-600 hover:bg-emerald-700')
                        .addClass('bg-slate-500');
                },
                error: xhr => {
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

    bindHandlers();

    $(() => initPickupPanel());
    $(document).on('livewire:navigated', initPickupPanel);
    initPickupPanel();
})(window.jQuery);
