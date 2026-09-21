(function () {
    'use strict';

    let initializedPanel = null;

    function getPanel() {
        return document.querySelector('[data-panel-name="hold"]');
    }

    function getCsrfToken() {
        const panel = getPanel();

        if (!panel) {
            return '';
        }

        return panel.dataset.csrfToken || '';
    }

    function getUrl(name) {
        const panel = getPanel();

        if (!panel) {
            return '';
        }

        return panel.dataset[name] || '';
    }

    function getContent() {
        return document.querySelector('[data-content]');
    }

    function getMessage() {
        return document.querySelector('[data-message]');
    }

    function showMessage(message, type = 'danger') {
        const element = getMessage();

        if (!element) {
            return;
        }

        element.className =
            'mt-4 rounded-lg border px-4 py-3 text-sm font-medium';

        if (type === 'success') {
            element.classList.add(
                'border-emerald-200',
                'bg-emerald-50',
                'text-emerald-700'
            );
        } else if (type === 'warning') {
            element.classList.add(
                'border-amber-200',
                'bg-amber-50',
                'text-amber-700'
            );
        } else {
            element.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700'
            );
        }

        element.textContent = message;
        element.classList.remove('hidden');
    }

    function hideMessage() {
        const element = getMessage();

        if (!element) {
            return;
        }

        element.textContent = '';
        element.classList.add('hidden');
    }

    function setLoading(button, loading, text = 'SEARCH') {
        if (!button) {
            return;
        }

        button.disabled = loading;

        if (loading) {
            button.dataset.originalText = button.textContent;
            button.textContent = 'SEARCHING...';
        } else {
            button.textContent =
                button.dataset.originalText || text;
        }
    }

    async function postJson(url, payload) {
        const response = await fetch(url, {
            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },

            body: JSON.stringify(payload),
        });

        let data = {};

        try {
            data = await response.json();
        } catch (error) {
            throw new Error(
                'Response server tidak dapat diproses.'
            );
        }

        if (!response.ok) {
            const error = new Error(
                data.message ||
                'Terjadi kesalahan pada server.'
            );

            error.status = response.status;
            error.response = data;

            throw error;
        }

        return data;
    }

    async function searchContainer(noCont) {
        const content = getContent();

        if (!content) {
            return;
        }

        hideMessage();

        content.innerHTML = `
            <div class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    Mencari container...
                </div>
            </div>
        `;

        try {
            const data = await postJson(
                getUrl('searchUrl'),
                {
                    search_cont: noCont,
                }
            );

            if (data.html) {
                content.innerHTML = data.html;
            } else {
                content.innerHTML = '';
            }

        } catch (error) {
            content.innerHTML = '';

            showMessage(
                error.message ||
                'Terjadi kesalahan saat mencari NO CONTAINER.',
                'danger'
            );
        }
    }

    async function getDetail(noCont) {
        const content = getContent();

        if (!content) {
            return;
        }

        hideMessage();

        content.innerHTML = `
            <div class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    Mengambil detail container...
                </div>
            </div>
        `;

        try {
            const data = await postJson(
                getUrl('detailUrl'),
                {
                    no_cont: noCont,
                }
            );

            if (data.html) {
                content.innerHTML = data.html;
            } else {
                content.innerHTML = '';
            }

        } catch (error) {
            content.innerHTML = '';

            showMessage(
                error.message ||
                'Terjadi kesalahan saat mengambil detail container.',
                'danger'
            );
        }
    }

    async function loadHeldContainers() {
        const container = document.querySelector('[data-hold-list]');

        if (!container) {
            return;
        }

        try {
            const response = await fetch(
                getUrl('dataUrl'),
                {
                    method: 'GET',

                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                }
            );

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.message ||
                    'Gagal mengambil data HOLD.'
                );
            }

            container.innerHTML = data.html || '';

        } catch (error) {
            container.innerHTML = `
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700">
                    ${escapeHtml(
                        error.message ||
                        'Gagal mengambil data container HOLD.'
                    )}
                </div>
            `;
        }
    }

    async function handleHoldSubmit(form) {
        const button = form.querySelector('[data-hold-submit]');
        const formData = new FormData(form);

        const warna = formData.get('warna');

        if (!warna) {
            showMessage(
                'Silakan pilih warna segel terlebih dahulu.',
                'warning'
            );

            return;
        }

        if (button) {
            button.disabled = true;
            button.textContent = 'MEMPROSES...';
        }

        try {
            const response = await fetch(
                form.action,
                {
                    method: 'POST',

                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },

                    body: formData,
                }
            );

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.message ||
                    'Container gagal diproses HOLD.'
                );
            }

            showMessage(
                data.message ||
                'Validasi HOLD berhasil.',
                'success'
            );

            /*
             * Read-only:
             * Tidak mengubah database.
             */
            if (button) {
                button.disabled = false;
                button.textContent = 'HOLD';
            }

        } catch (error) {
            showMessage(
                error.message ||
                'Terjadi kesalahan saat proses HOLD.',
                'danger'
            );

            if (button) {
                button.disabled = false;
                button.textContent = 'HOLD';
            }
        }
    }

    async function handleReleaseSubmit(form) {
        const button = form.querySelector('[data-release-submit]');

        if (button) {
            button.disabled = true;
            button.textContent = 'MEMPROSES...';
        }

        try {
            const formData = new FormData(form);

            const response = await fetch(
                form.action,
                {
                    method: 'POST',

                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },

                    body: formData,
                }
            );

            const data = await response.json();

            if (!response.ok) {
                throw new Error(
                    data.message ||
                    'Container gagal diproses RELEASE.'
                );
            }

            showMessage(
                data.message ||
                'Validasi RELEASE berhasil.',
                'success'
            );

            if (button) {
                button.disabled = false;
                button.textContent = 'RELEASE';
            }

        } catch (error) {
            showMessage(
                error.message ||
                'Terjadi kesalahan saat proses RELEASE.',
                'danger'
            );

            if (button) {
                button.disabled = false;
                button.textContent = 'RELEASE';
            }
        }
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }

    function init() {
        const panel = getPanel();

        if (!panel) {
            return;
        }

        if (initializedPanel === panel) {
            return;
        }

        initializedPanel = panel;

        /*
         * Search
         */
        const searchForm = panel.querySelector(
            '[data-search-form]'
        );

        if (searchForm) {
            searchForm.addEventListener(
                'submit',
                async function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    const input = searchForm.querySelector(
                        '[name="search_cont"]'
                    );

                    const button = searchForm.querySelector(
                        '[data-search-button]'
                    );

                    const noCont = (
                        input?.value || ''
                    ).trim().toUpperCase();

                    if (!noCont) {
                        showMessage(
                            'NO CONTAINER wajib diisi.',
                            'warning'
                        );

                        input?.focus();

                        return;
                    }

                    if (input) {
                        input.value = noCont;
                    }

                    setLoading(button, true);

                    try {
                        await searchContainer(noCont);
                    } finally {
                        setLoading(button, false);
                    }

                    return false;
                }
            );
        }

        /*
         * Delegated click:
         * pilih NO CONTAINER dari status 2
         */
        panel.addEventListener(
            'click',
            function (event) {
                const button = event.target.closest(
                    '[data-hold-detail]'
                );

                if (!button) {
                    return;
                }

                event.preventDefault();

                const noCont = (
                    button.dataset.noCont || ''
                ).trim().toUpperCase();

                if (!noCont) {
                    return;
                }

                getDetail(noCont);
            }
        );

        /*
         * Delegated submit:
         * HOLD dan RELEASE
         */
        panel.addEventListener(
            'submit',
            function (event) {
                const holdForm = event.target.closest(
                    '[data-hold-store-form]'
                );

                if (holdForm) {
                    event.preventDefault();
                    event.stopPropagation();

                    handleHoldSubmit(holdForm);

                    return;
                }

                const releaseForm = event.target.closest(
                    '[data-release-form]'
                );

                if (releaseForm) {
                    event.preventDefault();
                    event.stopPropagation();

                    handleReleaseSubmit(releaseForm);

                    return;
                }
            }
        );

        /*
         * Load existing HOLD.
         */
        loadHeldContainers();
    }

    /*
     * Dashboard menggunakan dynamic panel.
     */
    document.addEventListener(
        'DOMContentLoaded',
        init
    );

    document.addEventListener(
        'livewire:navigated',
        init
    );

})();