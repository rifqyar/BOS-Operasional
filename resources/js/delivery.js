document.addEventListener('submit', async function (event) {
    const searchForm = event.target.closest('[data-search-form]');
    const storeForm = event.target.closest('[data-store-form]');

    if (!searchForm && !storeForm) {
        return;
    }

    event.preventDefault();

    const form = searchForm || storeForm;
    const panel = form.closest('[data-panel]');

    if (!panel) {
        return;
    }

    const messageBox = panel.querySelector('[data-message]');
    const content = panel.querySelector('[data-content]');
    const csrfToken = panel.dataset.csrfToken;

    const showMessage = (message, type = 'error') => {
        if (!messageBox) return;

        messageBox.textContent = message;
        messageBox.classList.remove(
            'hidden',
            'bg-red-100',
            'text-red-700',
            'bg-green-100',
            'text-green-700',
            'bg-yellow-100',
            'text-yellow-700'
        );

        if (type === 'success') {
            messageBox.classList.add('bg-green-100', 'text-green-700');
        } else if (type === 'warning') {
            messageBox.classList.add('bg-yellow-100', 'text-yellow-700');
        } else {
            messageBox.classList.add('bg-red-100', 'text-red-700');
        }
    };

    const hideMessage = () => {
        if (messageBox) {
            messageBox.classList.add('hidden');
            messageBox.textContent = '';
        }
    };

    hideMessage();

    const submitButton = form.querySelector(
        '[data-search-button], [data-store-button]'
    );

    if (submitButton) {
        submitButton.disabled = true;
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
    }

    try {
        const formData = new FormData(form);

        const url = searchForm
            ? panel.dataset.searchUrl
            : panel.dataset.storeUrl;

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        });

        const result = await response.json();

        if (!response.ok) {
            if (response.status === 419) {
                throw new Error('Session telah berakhir. Silakan refresh halaman.');
            }

            throw new Error(
                result.message ||
                result.errors?.search_cont2?.[0] ||
                result.errors?.nomercont?.[0] ||
                'Permintaan gagal diproses.'
            );
        }

        if (searchForm) {
            if (Number(result.status) === 0) {
                if (content) content.innerHTML = '';
                showMessage(result.message || 'Container tidak ditemukan.');
                return;
            }

            if (content) {
                content.innerHTML = result.data || '';
            }

            showMessage('Data container berhasil ditemukan.', 'success');
            return;
        }

        if (result.status === 'success' || result.success === true) {
            showMessage(result.message || 'Delivery berhasil diproses.', 'success');

            if (content) {
                content.innerHTML = '';
            }

            return;
        }

        showMessage(
            result.message || 'Proses penyimpanan Delivery belum tersedia.'
        );
    } catch (error) {
        showMessage(error.message || 'Terjadi kesalahan pada Delivery.');
    } finally {
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
});

/**
 * Reset panel Delivery:
 * - Mengosongkan input pencarian
 * - Menghapus hasil form
 * - Menghapus pesan
 */
document.addEventListener('click', function (event) {
    const resetButton = event.target.closest('[data-reset-button]');

    if (!resetButton) {
        return;
    }

    const panel = resetButton.closest('[data-panel]');

    if (!panel) {
        return;
    }

    const searchForm = panel.querySelector('[data-search-form]');
    const content = panel.querySelector('[data-content]');
    const messageBox = panel.querySelector('[data-message]');

    if (searchForm) {
        searchForm.reset();

        const searchInput = searchForm.querySelector('[name="search_cont2"]');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
    }

    if (content) {
        content.innerHTML = '';
    }

    if (messageBox) {
        messageBox.textContent = '';
        messageBox.classList.add('hidden');
        messageBox.classList.remove(
            'bg-red-100',
            'text-red-700',
            'bg-green-100',
            'text-green-700',
            'bg-yellow-100',
            'text-yellow-700'
        );
    }
});