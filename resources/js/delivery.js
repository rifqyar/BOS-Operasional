document.addEventListener('submit', async function (event) {
    const searchForm = event.target.closest('[data-search-form]');
    const storeForm = event.target.closest('[data-store-form]');
    if (!searchForm && !storeForm) return;

    event.preventDefault();
    const form = searchForm || storeForm;
    const panel = form.closest('[data-panel]');
    if (!panel) return;

    const content = panel.querySelector('[data-content]');
    const csrfToken = panel.dataset.csrfToken || '';

    const showAlert = (icon, title, text) => {
        if (typeof window.showAlert === 'function') {
            window.showAlert(icon, title, text);
        } else if (window.Swal?.fire) {
            window.Swal.fire({
                icon,
                title,
                text,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#0369a1',
            });
        } else {
            window.alert(text);
        }
    };

    const showToast = (icon, title, text = '') => {
        if (typeof window.showToast === 'function') {
            window.showToast(icon, title, text);
        } else if (window.Swal?.fire) {
            window.Swal.fire({
                toast: true,
                position: 'top-end',
                icon,
                title: title || text,
                text: title ? text : '',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        } else {
            window.alert(text || title);
        }
    };

    const button = form.querySelector('[data-search-button], [data-store-button]');
    const defaultButtonHtml = button?.innerHTML || '';
    if (button) {
        button.disabled = true;
        button.classList.add('opacity-50', 'cursor-not-allowed');
            button.textContent = searchForm ? 'Mencari...' : 'Memproses...';
    }

    try {
        const url = searchForm ? panel.dataset.searchUrl : panel.dataset.storeUrl;
        if (!url) throw new Error('URL Delivery belum dikonfigurasi.');

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: new FormData(form),
        });
        const result = await response.json();

        if (!response.ok) {
            const error = new Error(
                result.message || result.errors?.search_cont2?.[0]
                || result.errors?.nomercont?.[0]
                || 'Permintaan Delivery gagal diproses.'
            );
            error.status = response.status;
            throw error;
        }

        if (searchForm) {
            if (result.status === 0 || result.status === 'info') {
                if (content) content.innerHTML = '';
                showAlert('info', 'Informasi', result.message || 'Container tidak ditemukan.');
                return;
            }

            if (content) content.innerHTML = result.data || '';
            showToast('info', 'Informasi', 'Data container berhasil ditemukan.');
            return;
        }

        if (result.status === 'success') {
            showToast('success', 'Berhasil', result.message || 'Delivery berhasil diproses.');
            if (content) content.innerHTML = '';
            return;
        }

        showAlert('info', 'Informasi', result.message || 'Delivery belum dapat diproses.');
    } catch (error) {
        if (error.status === 419) {
            showAlert('warning', 'Sesi berakhir', 'Silakan refresh halaman dan coba kembali.');
        } else if ([404, 409, 422].includes(error.status)) {
            showAlert('info', 'Informasi', error.message);
        } else {
            showAlert('error', 'Gagal', error.message || 'Terjadi kesalahan pada Delivery.');
        }
    } finally {
        if (button) {
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            button.innerHTML = defaultButtonHtml;
        }
    }
});

document.addEventListener('click', function (event) {
    const resetButton = event.target.closest('[data-reset-button]');
    if (!resetButton) return;

    const panel = resetButton.closest('[data-panel]');
    if (!panel) return;

    const searchForm = panel.querySelector('[data-search-form]');
    const content = panel.querySelector('[data-content]');
    if (searchForm) {
        searchForm.reset();
        const searchInput = searchForm.querySelector('[name="search_cont2"]');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
    }
    if (content) content.innerHTML = '';
});
