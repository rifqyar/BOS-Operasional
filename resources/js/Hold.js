(() => {
    'use strict';

    let panel = null;

    let holdData = { id: null, noCont: null, noSpk: null };
    let releaseData = { id: null, noCont: null, noSpk: null };


    function getPanel() {
        return document.querySelector('#hold-panel');
    }


    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }


    function showMessage(message, type = 'info') {
        const element = panel?.querySelector('#hold-message');

        if (!element) {
            return;
        }


        const colors = {
            success: ['bg-emerald-50', 'text-emerald-700'],
            error: ['bg-red-50', 'text-red-700'],
            warning: ['bg-amber-50', 'text-amber-700'],
            info: ['bg-zinc-100', 'text-zinc-700'],
        };

        element.className = 'mt-3 rounded-xl px-4 py-3 text-sm';
        element.classList.add(...(colors[type] || colors.info));
        element.classList.remove('hidden');
        element.textContent = message;
    }


    function setLoading(loading) {
        const button = panel?.querySelector('#hold-search-button');

        if (!button) {
            return;
        }


        button.disabled = loading;
        const text = button.querySelector('span');

        if (text) {

            text.textContent = loading ? 'Mencari...' : 'Cari';
        }
    }


    async function request(url, payload = {}) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        });
        const data = await response.json().catch(() => null);

        if (!response.ok) {

            throw new Error(data?.message || 'Request gagal diproses.');
        }


        return data;
    }


    async function loadHeldData() {

        const container =
            panel?.querySelector(
                '#hold-form-container'
            );

        if (!container) {
            return;
        }


        try {

            const response = await fetch(
                panel.dataset.dataUrl,
                {
                    method: 'GET',

                    headers: {
                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest',
                    },
                }
            );


            const data =
                await response.json();


            if (!response.ok || !data.success) {

                throw new Error(
                    data.message ||
                    'Gagal memuat data HOLD.'
                );
            }


            container.innerHTML =
                data.html || '';


        } catch (error) {

            container.innerHTML = `
                <div class="rounded-2xl border border-red-200 bg-red-50 p-6 text-center dark:border-red-900/50 dark:bg-red-900/20">
                    <div class="text-sm text-red-700 dark:text-red-400">
                        ${error.message || 'Gagal memuat data HOLD.'}
                    </div>
                </div>
            `;
        }
    }


    async function search() {

        const input =
            panel?.querySelector(
                '#hold-search-input'
            );

        if (!input) {
            return;
        }


        const keyword =
            input.value
                .trim()
                .toUpperCase();


        if (!keyword) {

            showMessage(
                'Nomor container wajib diisi.',
                'warning'
            );

            input.focus();

            return;
        }


        setLoading(true);


        try {

            const response =
                await request(
                    panel.dataset.searchUrl,
                    {
                        search_cont:
                            keyword,
                    }
                );


            if (!response.success) {

                throw new Error(
                    response.message ||
                    'Search gagal.'
                );
            }


            const container =
                panel.querySelector(
                    '#hold-form-container'
                );


            if (container) {

                container.innerHTML =
                    response.html || `
                        <div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-8 text-center dark:border-zinc-700 dark:bg-zinc-900">
                            <div class="text-sm text-zinc-500">
                                Container tidak ditemukan.
                            </div>
                        </div>
                    `;
            }


            if (response.status === 2) {

                showMessage(
                    'Container ditemukan dan siap untuk di-HOLD.',
                    'success'
                );

            } else if (response.status === 3) {

                showMessage(
                    'Container sedang dalam kondisi HOLD.',
                    'warning'
                );

            } else {

                showMessage(
                    `Container ${keyword} tidak ditemukan.`,
                    'error'
                );
            }


        } catch (error) {

            showMessage(
                error.message ||
                'Terjadi kesalahan saat search.',
                'error'
            );

        } finally {

            setLoading(false);
        }
    }


    function openHoldModal(button) {

        const modal =
            panel?.querySelector(
                '#hold-modal'
            );

        if (!modal) {
            return;
        }


        holdData = {
            id:
                button.dataset.id,

            noCont:
                button.dataset.noCont,

            noSpk:
                button.dataset.noSpk || null,
        };


        const container =
            modal.querySelector(
                '#hold-modal-container'
            );


        if (container) {

            container.textContent =
                holdData.noCont;
        }


        const defaultRadio =
            modal.querySelector(
                'input[name="hold_warna"][value="N"]'
            );


        if (defaultRadio) {
            defaultRadio.checked = true;
        }


        modal.classList.remove(
            'hidden'
        );
    }


    function closeHoldModal() {

        const modal =
            panel?.querySelector(
                '#hold-modal'
            );


        if (modal) {

            modal.classList.add(
                'hidden'
            );
        }


        holdData = {
            id: null,
            noCont: null,
            noSpk: null,
        };
    }


    async function submitHold() {

        const modal =
            panel?.querySelector(
                '#hold-modal'
            );


        if (!modal) {
            return;
        }


        const selected =
            modal.querySelector(
                'input[name="hold_warna"]:checked'
            );


        if (!selected) {

            showMessage(
                'Silakan pilih warna HOLD.',
                'warning'
            );

            return;
        }


        const button =
            modal.querySelector(
                '#hold-modal-submit'
            );


        if (button) {
            button.disabled = true;
        }


        try {

            const response =
                await request(
                    panel.dataset.storeUrl,
                    {
                        id:
                            holdData.id,

                        nomercont:
                            holdData.noCont,

                        warna:
                            selected.value,
                    }
                );


            if (!response.success) {

                throw new Error(
                    response.message ||
                    'HOLD gagal.'
                );
            }


            closeHoldModal();


            showMessage(
                response.message ||
                'Container berhasil di-HOLD.',
                'success'
            );


            await loadHeldData();


        } catch (error) {

            showMessage(
                error.message ||
                'Container gagal di-HOLD.',
                'error'
            );

        } finally {

            if (button) {
                button.disabled = false;
            }
        }
    }


    function openReleaseModal(button) {

        const modal =
            panel?.querySelector(
                '#release-modal'
            );


        if (!modal) {
            return;
        }


        releaseData = {
            id:
                button.dataset.id,

            noCont:
                button.dataset.noCont,

            noSpk:
                button.dataset.noSpk || null,
        };


        const container =
            modal.querySelector(
                '#release-modal-container'
            );


        if (container) {

            container.textContent =
                releaseData.noCont;
        }


        modal.classList.remove(
            'hidden'
        );
    }


    function closeReleaseModal() {

        const modal =
            panel?.querySelector(
                '#release-modal'
            );


        if (modal) {

            modal.classList.add(
                'hidden'
            );
        }


        releaseData = {
            id: null,
            noCont: null,
            noSpk: null,
        };
    }


    async function submitRelease() {

        const button =
            panel?.querySelector(
                '#release-modal-submit'
            );


        if (button) {
            button.disabled = true;
        }


        try {

            const response =
                await request(
                    panel.dataset.releaseUrl,
                    {
                        id:
                            releaseData.id,

                        nomercont:
                            releaseData.noCont,

                        nospk:
                            releaseData.noSpk,
                    }
                );


            if (!response.success) {

                throw new Error(
                    response.message ||
                    'RELEASE gagal.'
                );
            }


            closeReleaseModal();


            showMessage(
                response.message ||
                'Container berhasil di-RELEASE.',
                'success'
            );


            await loadHeldData();


        } catch (error) {

            showMessage(
                error.message ||
                'Container gagal di-RELEASE.',
                'error'
            );

        } finally {

            if (button) {
                button.disabled = false;
            }
        }
    }


    function bindEvents() {

        panel = getPanel();


        if (!panel) {
            return;
        }


        const searchForm =
            panel.querySelector(
                '#hold-search-form'
            );


        if (searchForm) {

            searchForm.addEventListener(
                'submit',
                (event) => {

                    event.preventDefault();

                    search();
                }
            );
        }


        panel.addEventListener(
            'click',
            (event) => {

                const holdButton =
                    event.target.closest(
                        '.hold-open-button'
                    );


                if (holdButton) {

                    openHoldModal(
                        holdButton
                    );

                    return;
                }


                const releaseButton =
                    event.target.closest(
                        '.hold-release-button'
                    );


                if (releaseButton) {

                    openReleaseModal(
                        releaseButton
                    );

                    return;
                }


                if (
                    event.target.closest(
                        '#hold-modal-close'
                    ) ||
                    event.target.closest(
                        '#hold-modal-cancel'
                    )
                ) {

                    closeHoldModal();

                    return;
                }


                if (
                    event.target.closest(
                        '#release-modal-close'
                    ) ||
                    event.target.closest(
                        '#release-modal-cancel'
                    )
                ) {

                    closeReleaseModal();

                    return;
                }


                if (
                    event.target.closest(
                        '#hold-modal-submit'
                    )
                ) {

                    submitHold();

                    return;
                }


                if (
                    event.target.closest(
                        '#release-modal-submit'
                    )
                ) {

                    submitRelease();

                    return;
                }

            }
        );


        loadHeldData();
    }


    document.addEventListener(
        'DOMContentLoaded',
        bindEvents
    );


    document.addEventListener(
        'livewire:navigated',
        () => {

            panel = null;

            bindEvents();
        }
    );

})();