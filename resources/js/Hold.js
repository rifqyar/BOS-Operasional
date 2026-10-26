document.addEventListener('DOMContentLoaded', () => {
    const panel = document.querySelector('[data-panel-name="hold"]');

    if (!panel) {
        return;
    }

    const searchForm = panel.querySelector('[data-search-form]');
    const messageBox = panel.querySelector('[data-message]');
    const resultBox = panel.querySelector('[data-result]');
    const rowsBox = panel.querySelector('[data-rows]');
    const holdRowsBox = panel.querySelector('[data-hold-rows]');

    const urls = {
        search: panel.dataset.searchUrl,
        store: panel.dataset.storeUrl,
        release: panel.dataset.releaseUrl,
        data: panel.dataset.dataUrl,
    };

    const csrfToken = panel.dataset.csrfToken;

    function showMessage(message, type = 'success') {
        if (!messageBox) {
            window.alert(message);
            return;
        }

        messageBox.textContent = message;
        messageBox.classList.remove('hidden');

        messageBox.classList.toggle('text-red-600', type === 'error');
        messageBox.classList.toggle('text-green-600', type === 'success');
    }

    function clearMessage() {
        if (!messageBox) {
            return;
        }

        messageBox.textContent = '';
        messageBox.classList.add('hidden');
    }

    function headers() {
        return {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        };
    }

    async function requestJson(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                ...headers(),
                ...(options.headers || {}),
            },
        });

        let data;

        try {
            data = await response.json();
        } catch {
            throw new Error('Respons server tidak valid.');
        }

        if (!response.ok || data.success === false) {
            throw new Error(
                data.message || 'Permintaan gagal diproses.'
            );
        }

        return data;
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        })[character]);
    }

    function renderSearchResults(items) {
        if (!rowsBox) {
            return;
        }

        if (!items.length) {
            rowsBox.innerHTML = `
                <p class="p-4 text-sm text-slate-500">
                    Container tidak ditemukan.
                </p>
            `;
            return;
        }

        rowsBox.innerHTML = items.map(item => `
            <button
                type="button"
                class="block w-full border-b p-3 text-left hover:bg-slate-100"
                data-hold-detail
                data-id="${escapeHtml(item.ID)}"
            >
                <span class="font-semibold">
                    ${escapeHtml(item.NO_CONT)}
                </span>
                <span class="ml-2 text-sm text-slate-500">
                    SPK ${escapeHtml(item.NO_SPK)}
                </span>
            </button>
        `).join('');
    }

    async function searchContainer(searchValue) {
        clearMessage();

        try {
            const data = await requestJson(urls.search, {
                method: 'POST',
                body: JSON.stringify({
                    search_cont: searchValue,
                }),
            });

            renderSearchResults(data.data || []);

            if (resultBox) {
                resultBox.classList.remove('hidden');
            }
        } catch (error) {
            if (rowsBox) {
                rowsBox.innerHTML = '';
            }

            showMessage(error.message, 'error');
        }
    }

    async function loadHeldContainers() {
        if (!holdRowsBox) {
            return;
        }

        try {
            const data = await requestJson(urls.data, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            holdRowsBox.innerHTML = data.html || '';
        } catch (error) {
            holdRowsBox.innerHTML = `
                <div class="p-4 text-sm text-red-600">
                    ${escapeHtml(error.message)}
                    <button
                        type="button"
                        data-retry-hold
                        class="ml-2 underline"
                    >
                        Coba lagi
                    </button>
                </div>
            `;
        }
    }

    async function loadHoldDetail(id) {
        clearMessage();

        try {
            const data = await requestJson(
                panel.dataset.detailUrl || '/hold/detail',
                {
                    method: 'POST',
                    body: JSON.stringify({ id }),
                }
            );

            const item = data.data;

            /*
             * Controller detail mengembalikan JSON.
             * Form Blade yang sudah ada membutuhkan status dan item.
             * Endpoint ini tidak merender HTML, sehingga gunakan
             * endpoint detail khusus untuk mengambil form jika diperlukan.
             */
            showMessage(
                `Container ${item.NO_CONT} ditemukan. Lanjutkan melalui form Hold.`,
                'success'
            );
        } catch (error) {
            showMessage(error.message, 'error');
        }
    }

    async function submitHold(form) {
        clearMessage();

        const formData = new FormData(form);

        const id = formData.get('id');
        const warna = formData.get('warna');

        if (!id || !warna) {
            showMessage(
                'ID container dan warna Hold wajib diisi.',
                'error'
            );
            return;
        }

        const button = form.querySelector('[data-hold-submit]');

        if (button) {
            button.disabled = true;
        }

        try {
            const data = await requestJson(urls.store, {
                method: 'POST',
                body: JSON.stringify({
                    id,
                    warna,
                }),
            });

            showMessage(data.message || 'Hold berhasil.', 'success');

            form.reset();

            await loadHeldContainers();
        } catch (error) {
            showMessage(error.message, 'error');
        } finally {
            if (button) {
                button.disabled = false;
            }
        }
    }

    async function releaseContainer(id, noCont = '') {
        clearMessage();

        const confirmed = window.confirm(
            `Apakah kamu yakin ingin Release container ${noCont || id}?`
        );

        if (!confirmed) {
            return;
        }

        try {
            const data = await requestJson(urls.release, {
                method: 'POST',
                body: JSON.stringify({ id }),
            });

            showMessage(data.message || 'Release berhasil.', 'success');

            await loadHeldContainers();
        } catch (error) {
            showMessage(error.message, 'error');
        }
    }

    if (searchForm) {
        searchForm.addEventListener('submit', event => {
            event.preventDefault();

            const input = searchForm.querySelector(
                '[name="search_cont"]'
            );

            const searchValue = input?.value.trim() || '';

            if (!searchValue) {
                showMessage(
                    'Masukkan nomor container terlebih dahulu.',
                    'error'
                );
                return;
            }

            searchContainer(searchValue);
        });
    }

    panel.addEventListener('submit', event => {
        const form = event.target.closest('[data-hold-store-form]');

        if (!form) {
            return;
        }

        event.preventDefault();
        submitHold(form);
    });

    panel.addEventListener('click', event => {
        const detailButton = event.target.closest('[data-hold-detail]');

        if (detailButton) {
            loadHoldDetail(detailButton.dataset.id);
            return;
        }

        const releaseButton = event.target.closest(
            '[data-release-row], [data-release-form-button]'
        );

        if (releaseButton) {
            event.preventDefault();

            const form = releaseButton.closest('[data-release-form]');

            const id =
                releaseButton.dataset.id ||
                form?.querySelector('[name="id"]')?.value;

            const noCont =
                releaseButton.dataset.noCont ||
                form?.querySelector('[name="nomercont"]')?.value ||
                '';

            if (id) {
                releaseContainer(id, noCont);
            }

            return;
        }

        if (event.target.closest('[data-retry-hold]')) {
            loadHeldContainers();
        }
    });

    loadHeldContainers();
});