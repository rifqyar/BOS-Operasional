(($) => {
    if (! $) {
        return;
    }

    /* ------------------------------------------------------------------ */
    /*  Global UI helpers                                                 */
    /* ------------------------------------------------------------------ */

    window.showAlert = (icon, title, text) => {
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

    window.setButtonLoading = ($button, isLoading, label) => {
        $button.prop('disabled', isLoading);
        $button.html(
            isLoading
                ? '<span class="inline-block size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Memproses'
                : label
        );
    };

    /* ------------------------------------------------------------------ */
    /*  Core navigation & redirection                                      */
    /* ------------------------------------------------------------------ */

    window.navigateTo = (url) => {
        if (! url) return;

        if (window.Livewire && typeof window.Livewire.navigate === 'function') {
            window.Livewire.navigate(url);
            return;
        }

        window.location.href = url;
    };

    /* ------------------------------------------------------------------ */
    /*  Core AJAX setup (CSRF token)                                      */
    /* ------------------------------------------------------------------ */

    const setupAjaxCsrf = () => {
        const token = $('meta[name="csrf-token"]').attr('content');
        if (token) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token,
                },
            });
        }
    };

    /* ------------------------------------------------------------------ */
    /*  Core Handheld UI event handlers                                   */
    /* ------------------------------------------------------------------ */

    const bindCoreHandlers = () => {
        setupAjaxCsrf();

        /* ---- Global Handheld Reload button ---- */
        $(document).off('click.bosReloadHandheld', '[data-reload-handheld]');
        $(document).on('click.bosReloadHandheld', '[data-reload-handheld]', function () {
            const $button = $(this);
            const originalHtml = $button.html();

            $button.prop('disabled', true);
            $button.html(
                '<span class="inline-block size-5 animate-spin rounded-full border-2 border-slate-400/40 border-t-sky-600 dark:border-white/30 dark:border-t-white"></span>'
            );

            // Broadcast reload event so the currently active page/panel can refresh its data
            $(document).trigger('bos:reload-data');

            setTimeout(() => {
                $button.prop('disabled', false);
                $button.html(originalHtml);
            }, 800);
        });

        /* ---- Generic data-navigate links ---- */
        $(document).off('click.bosNavigate', '[data-navigate]');
        $(document).on('click.bosNavigate', '[data-navigate]', function (event) {
            event.preventDefault();
            const url = $(this).attr('href') || $(this).data('navigate');
            window.navigateTo(url);
        });
    };

    // Initialize core handlers
    bindCoreHandlers();

    // Re-initialize on Livewire SPA page transitions
    $(document).on('livewire:navigated', () => {
        bindCoreHandlers();
    });
})(window.jQuery);
