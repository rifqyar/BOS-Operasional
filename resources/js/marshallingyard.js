(function ($) {
    'use strict';

    const PANEL_SELECTOR =
        '[data-panel][data-panel-name="marshallingyard"]';

    function getPanel() {
        return document.querySelector(PANEL_SELECTOR);
    }

    function getUrl(panel, attribute) {
        return panel?.dataset?.[attribute] || '';
    }

    function getCsrf(panel) {
        return panel?.dataset?.csrfToken || '';
    }

    function showAlert(message, type = 'error') {
        if (typeof window.showAlert === 'function') {
            window.showAlert(message, type);
            return;
        }

        if (window.Swal) {
            Swal.fire({
                icon: type === 'success' ? 'success' : 'error',
                text: message,
            });
            return;
        }

        window.alert(message);
    }

    function setLoading(button, loading, textLoading = 'Memproses...') {
        if (!button) {
            return;
        }

        if (loading) {
            button.dataset.originalText =
                button.innerHTML;

            button.innerHTML = textLoading;
            button.disabled = true;
        } else {
            button.innerHTML =
                button.dataset.originalText || 'Search';

            button.disabled = false;
        }
    }

    function setDataCount(panel, count) {
        const element =
            panel.querySelector('[data-data-count]');

        if (!element) {
            return;
        }

        element.textContent = `${count} data`;
    }

    function countRows(html) {
        const temp = document.createElement('div');

        temp.innerHTML = html;

        return temp.querySelectorAll(
            'tbody > tr'
        ).length;
    }

    function loadInitialData(panel) {
        const url = getUrl(panel, 'dataUrl');

        if (!url) {
            return;
        }

        const table =
            panel.querySelector('[data-table]');

        if (!table) {
            return;
        }

        table.innerHTML = `
            <div class="px-4 py-10 text-center text-sm text-slate-500">
                Memuat data Marshalling Yard...
            </div>
        `;

        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': getCsrf(panel),
                'Accept': 'application/json',
            },
        })
            .done(function (response) {
                table.innerHTML =
                    response.data || '';

                setDataCount(
                    panel,
                    countRows(response.data || '')
                );
            })
            .fail(function (xhr) {
                const message =
                    xhr.responseJSON?.message ||
                    'Gagal mengambil data Marshalling Yard.';

                table.innerHTML = `
                    <div class="px-4 py-10 text-center text-sm text-rose-600">
                        ${message}
                    </div>
                `;

                setDataCount(panel, 0);
            });
    }

    function searchData(panel, form) {
        const url =
            getUrl(panel, 'searchUrl');

        if (!url) {
            return;
        }

        const button =
            form.querySelector('[data-search-button]');

        const noCont =
            form.querySelector('[name="no_cont"]')?.value
                ?.trim() || '';

        if (!noCont) {
            showAlert(
                'Nomor container wajib diisi.',
                'error'
            );

            return;
        }

        setLoading(
            button,
            true,
            'Mencari...'
        );

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                no_cont: noCont,
            },
            headers: {
                'X-CSRF-TOKEN': getCsrf(panel),
                'Accept': 'application/json',
            },
        })
            .done(function (response) {
                const table =
                    panel.querySelector('[data-table]');

                if (table) {
                    table.innerHTML =
                        response.data || '';
                }

                setDataCount(
                    panel,
                    countRows(response.data || '')
                );

                if (response.message) {
                    showAlert(
                        response.message,
                        'error'
                    );
                }
            })
            .fail(function (xhr) {
                const message =
                    xhr.responseJSON?.message ||
                    'Gagal mencari data Marshalling Yard.';

                showAlert(
                    message,
                    'error'
                );
            })
            .always(function () {
                setLoading(
                    button,
                    false,
                    'Search'
                );
            });
    }

    function loadDetail(panel, button) {
        const url =
            getUrl(panel, 'detailUrl');

        if (!url) {
            return;
        }

        const idJobSlip =
            button.dataset.idJobSlip;

        if (!idJobSlip) {
            showAlert(
                'ID Job Slip tidak ditemukan.',
                'error'
            );

            return;
        }

        const detail =
            panel.querySelector('[data-detail]');

        if (!detail) {
            return;
        }

        button.disabled = true;
        button.dataset.originalText =
            button.innerHTML;

        button.innerHTML =
            'Memuat...';

        detail.classList.remove('hidden');

        detail.innerHTML = `
            <div
                class="rounded-2xl bg-white p-6 text-center
                       shadow-sm ring-1 ring-slate-200
                       dark:bg-slate-900 dark:ring-slate-800"
            >
                <div class="text-sm text-slate-500">
                    Memuat detail Marshalling Yard...
                </div>
            </div>
        `;

        detail.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                id_job_slip: idJobSlip,
            },
            headers: {
                'X-CSRF-TOKEN': getCsrf(panel),
                'Accept': 'application/json',
            },
        })
            .done(function (response) {
                detail.innerHTML =
                    response.data || '';

                bindDependentFields(detail);
            })
            .fail(function (xhr) {
                const message =
                    xhr.responseJSON?.message ||
                    'Gagal mengambil detail Marshalling Yard.';

                detail.innerHTML = `
                    <div
                        class="rounded-2xl border border-rose-200
                               bg-rose-50 px-4 py-5 text-sm
                               text-rose-700"
                    >
                        ${message}
                    </div>
                `;

                showAlert(
                    message,
                    'error'
                );
            })
            .always(function () {
                button.innerHTML =
                    button.dataset.originalText ||
                    'PROSES';

                button.disabled = false;
            });
    }

    function bindDependentFields(scope) {
        $(scope)
            .find('[data-activity]')
            .off('change.marshallingyard')
            .on(
                'change.marshallingyard',
                function () {
                    const activity =
                        String(this.value || '');

                    const group =
                        this.dataset.activity;

                    const dependent =
                        scope.querySelectorAll(
                            `[data-dependent="${group}"]`
                        );

                    dependent.forEach(function (element) {
                        const enabled =
                            activity !== '' &&
                            activity !== '0';

                        element.disabled =
                            !enabled;

                        if (!enabled) {
                            element.value = '';
                        }
                    });
                }
            );

        $(scope)
            .find('[data-activity]')
            .trigger('change.marshallingyard');
    }

    function storeData(panel, form) {
        const url =
            getUrl(panel, 'storeUrl');

        if (!url) {
            return;
        }

        const button =
            form.querySelector('[data-store-button]');

        const formData =
            $(form).serialize();

        setLoading(
            button,
            true,
            'Menyimpan...'
        );

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': getCsrf(panel),
                'Accept': 'application/json',
            },
        })
            .done(function (response) {
                showAlert(
                    response.message ||
                    'Data Marshalling Yard berhasil divalidasi.',
                    'success'
                );
            })
            .fail(function (xhr) {
                const message =
                    xhr.responseJSON?.message ||
                    'Gagal memproses Marshalling Yard.';

                showAlert(
                    message,
                    'error'
                );
            })
            .always(function () {
                setLoading(
                    button,
                    false,
                    'Simpan Marshalling Yard'
                );
            });
    }

    function bindPanel(panel) {
        if (!panel || panel.dataset.bound === '1') {
            return;
        }

        panel.dataset.bound = '1';

        const namespace =
            '.marshallingyard';

        $(panel)
            .off(`submit${namespace}`, '[data-search-form]')
            .on(
                `submit${namespace}`,
                '[data-search-form]',
                function (event) {
                    event.preventDefault();

                    searchData(
                        panel,
                        this
                    );
                }
            );

        $(panel)
            .off(`click${namespace}`, '[data-reset-button]')
            .on(
                `click${namespace}`,
                '[data-reset-button]',
                function () {
                    const input =
                        panel.querySelector(
                            '[name="no_cont"]'
                        );

                    if (input) {
                        input.value = '';
                        input.focus();
                    }

                    loadInitialData(panel);
                }
            );

        $(panel)
            .off(`click${namespace}`, '[data-detail-button]')
            .on(
                `click${namespace}`,
                '[data-detail-button]',
                function () {
                    loadDetail(
                        panel,
                        this
                    );
                }
            );

        $(panel)
            .off(`submit${namespace}`, '[data-store-form]')
            .on(
                `submit${namespace}`,
                '[data-store-form]',
                function (event) {
                    event.preventDefault();

                    storeData(
                        panel,
                        this
                    );
                }
            );

        $(panel)
            .off(`click${namespace}`, '[data-close-detail]')
            .on(
                `click${namespace}`,
                '[data-close-detail]',
                function () {
                    const detail =
                        panel.querySelector(
                            '[data-detail]'
                        );

                    if (!detail) {
                        return;
                    }

                    detail.classList.add(
                        'hidden'
                    );

                    detail.innerHTML = '';

                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth',
                    });
                }
            );

        loadInitialData(panel);
    }

    function initMarshallingYard() {
        const panel =
            getPanel();

        if (!panel) {
            return;
        }

        bindPanel(panel);
    }

    $(document).ready(function () {
        initMarshallingYard();
    });

    document.addEventListener(
        'livewire:navigated',
        function () {
            initMarshallingYard();
        }
    );

    document.addEventListener(
        'bos:reload-data',
        function () {
            const panel =
                getPanel();

            if (panel) {
                loadInitialData(panel);
            }
        }
    );

})(jQuery);