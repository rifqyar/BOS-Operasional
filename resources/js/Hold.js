document.addEventListener('DOMContentLoaded', () => {
    initHold();
});


function initHold() {

    const panel = document.querySelector(
        '[data-panel-name="hold"]'
    );

    if (!panel) {
        return;
    }


    /*
     * Hindari init dua kali.
     */
    if (panel.dataset.holdInitialized === 'true') {
        return;
    }

    panel.dataset.holdInitialized = 'true';


    /*
     * =========================================================
     * ELEMENT
     * =========================================================
     */

    const searchForm = panel.querySelector(
        '[data-search-form]'
    );

    const searchButton = panel.querySelector(
        '[data-search-button]'
    );

    const searchInput = panel.querySelector(
        'input[name="search_cont"]'
    );

    const message = panel.querySelector(
        '[data-message]'
    );

    const result = panel.querySelector(
        '[data-result]'
    );

    const rows = panel.querySelector(
        '[data-rows]'
    );

    const holdRows = panel.querySelector(
        '[data-hold-rows]'
    );


    /*
     * =========================================================
     * URL
     * =========================================================
     */

    function getUrl(name) {
        return panel.dataset[name] || '';
    }


    /*
     * =========================================================
     * CSRF
     * =========================================================
     */

    function getCsrfToken() {

        return (
            panel.dataset.csrfToken ||
            document
                .querySelector(
                    'meta[name="csrf-token"]'
                )
                ?.getAttribute('content') ||
            ''
        );
    }


    /*
     * =========================================================
     * HEADERS
     * =========================================================
     */

    function getHeaders() {

        return {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        };
    }


    /*
     * =========================================================
     * MESSAGE
     * =========================================================
     */

    function showMessage(
        text,
        type = 'error'
    ) {

        if (!message) {
            return;
        }


        message.className =
            'mt-4 rounded-lg border px-4 py-3 text-sm';


        if (type === 'success') {

            message.classList.add(
                'border-emerald-200',
                'bg-emerald-50',
                'text-emerald-700',
                'dark:border-emerald-400/20',
                'dark:bg-emerald-400/10',
                'dark:text-emerald-300'
            );

        } else if (type === 'warning') {

            message.classList.add(
                'border-amber-200',
                'bg-amber-50',
                'text-amber-700',
                'dark:border-amber-400/20',
                'dark:bg-amber-400/10',
                'dark:text-amber-300'
            );

        } else {

            message.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700',
                'dark:border-red-400/20',
                'dark:bg-red-400/10',
                'dark:text-red-300'
            );
        }


        message.textContent = text;

        message.classList.remove(
            'hidden'
        );
    }


    function hideMessage() {

        if (!message) {
            return;
        }


        message.classList.add(
            'hidden'
        );

        message.textContent = '';
    }


    /*
     * =========================================================
     * SEARCH LOADING
     * =========================================================
     */

    function setSearchLoading(
        loading
    ) {

        if (!searchButton) {
            return;
        }


        searchButton.disabled =
            loading;


        if (loading) {

            searchButton.dataset.originalHtml =
                searchButton.innerHTML;


            searchButton.innerHTML = `
                <svg
                    class="size-4 animate-spin"
                    viewBox="0 0 24 24"
                    fill="none"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>

                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                    ></path>
                </svg>

                Searching...
            `;

        } else {

            if (
                searchButton.dataset.originalHtml
            ) {

                searchButton.innerHTML =
                    searchButton.dataset.originalHtml;
            }
        }
    }


    /*
     * =========================================================
     * TABLE LOADING
     * =========================================================
     */

    function showTableLoading() {

        if (!holdRows) {
            return;
        }


        holdRows.innerHTML = `
            <div class="px-4 py-8 text-center">

                <div class="inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">

                    <svg
                        class="size-4 animate-spin"
                        viewBox="0 0 24 24"
                        fill="none"
                    >

                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                        ></path>

                    </svg>

                    Memuat data HOLD...

                </div>

            </div>
        `;
    }


    /*
     * =========================================================
     * SEARCH CONTAINER
     * =========================================================
     */

    async function searchContainer(
        keyword
    ) {

        const url =
            getUrl('searchUrl');


        if (!url) {

            showMessage(
                'URL pencarian HOLD tidak ditemukan.'
            );

            return;
        }


        hideMessage();

        setSearchLoading(true);


        try {

            const response =
                await fetch(
                    url,
                    {
                        method: 'POST',

                        headers:
                            getHeaders(),

                        body:
                            JSON.stringify({
                                search_cont:
                                    keyword,
                            }),
                    }
                );


            const data =
                await response.json();


            /*
             * Validation.
             */
            if (
                response.status === 422
            ) {

                const errors =
                    data.errors || {};


                const firstError =
                    Object.values(
                        errors
                    )[0]?.[0];


                showMessage(
                    firstError ||
                    'Data pencarian tidak valid.'
                );

                return;
            }


            /*
             * Not found.
             */
            if (
                response.status === 404 ||
                data.status === 0
            ) {

                if (result) {
                    result.classList.add(
                        'hidden'
                    );
                }


                showMessage(
                    data.message ||
                    'Container tidak ditemukan.',
                    'warning'
                );

                return;
            }


            /*
             * Server error.
             */
            if (!response.ok) {

                showMessage(
                    data.message ||
                    'Terjadi kesalahan pada server.'
                );

                return;
            }


            /*
             * Success.
             */
            if (
                data.success &&
                data.html
            ) {

                if (result) {

                    result.classList.remove(
                        'hidden'
                    );
                }


                if (rows) {

                    rows.innerHTML =
                        data.html;
                }


                hideMessage();


                result?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                });
            }


        } catch (error) {

            console.error(
                'HOLD SEARCH ERROR:',
                error
            );


            showMessage(
                'Tidak dapat terhubung ke server.'
            );


        } finally {

            setSearchLoading(false);
        }
    }


    /*
     * =========================================================
     * LOAD DATA HOLD
     * =========================================================
     */

    async function loadHeldContainers(
        page = 1
    ) {

        const url =
            getUrl('dataUrl');


        if (
            !url ||
            !holdRows
        ) {
            return;
        }


        showTableLoading();


        try {

            const separator =
                url.includes('?')
                    ? '&'
                    : '?';


            const requestUrl =
                `${url}${separator}page=${page}&per_page=10`;


            const response =
                await fetch(
                    requestUrl,
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


            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Gagal mengambil data HOLD.'
                );
            }


            if (!data.success) {

                throw new Error(
                    data.message ||
                    'Gagal mengambil data HOLD.'
                );
            }


            holdRows.innerHTML =
                data.html;


        } catch (error) {

            console.error(
                'HOLD DATA ERROR:',
                error
            );


            holdRows.innerHTML = `
                <div class="px-4 py-8 text-center">

                    <p class="text-sm font-semibold text-red-600 dark:text-red-400">
                        Gagal mengambil data HOLD.
                    </p>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        ${escapeHtml(error.message)}
                    </p>

                    <button
                        type="button"
                        data-retry-hold
                        class="mt-4 inline-flex items-center rounded-md bg-sky-700 px-4 py-2 text-xs font-semibold text-white hover:bg-sky-800"
                    >
                        Coba Lagi
                    </button>

                </div>
            `;
        }
    }


    /*
     * =========================================================
     * RELEASE
     * =========================================================
     */

    async function releaseContainer(
        button
    ) {

        const url =
            getUrl('releaseUrl');


        if (!url) {

            showMessage(
                'URL release tidak ditemukan.'
            );

            return;
        }


        const id =
            button.dataset.id || '';


        const noCont =
            button.dataset.noCont || '';


        const noSpk =
            button.dataset.noSpk || '';


        if (
            !id ||
            !noCont
        ) {

            showMessage(
                'Data container untuk release tidak lengkap.'
            );

            return;
        }


        const confirmed =
            window.confirm(
                `Release container ${noCont}?`
            );


        if (!confirmed) {
            return;
        }


        button.disabled =
            true;


        const originalHtml =
            button.innerHTML;


        button.innerHTML = `
            <svg
                class="size-3.5 animate-spin"
                viewBox="0 0 24 24"
                fill="none"
            >

                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                ></path>

            </svg>

            Processing...
        `;


        try {

            const response =
                await fetch(
                    url,
                    {
                        method: 'POST',

                        headers:
                            getHeaders(),

                        body:
                            JSON.stringify({
                                id:
                                    id,

                                nomercont:
                                    noCont,

                                nospk:
                                    noSpk,
                            }),
                    }
                );


            const data =
                await response.json();


            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Container gagal diproses.'
                );
            }


            if (!data.success) {

                throw new Error(
                    data.message ||
                    'Container gagal diproses.'
                );
            }


            showMessage(
                data.message ||
                'Validasi RELEASE berhasil.',
                'success'
            );


            /*
             * Backend masih read-only.
             *
             * Table tetap di-refresh
             * supaya siap ketika UPDATE
             * nanti diaktifkan.
             */
            await loadHeldContainers(1);


        } catch (error) {

            console.error(
                'HOLD RELEASE ERROR:',
                error
            );


            showMessage(
                error.message ||
                'Container gagal diproses.'
            );


            button.disabled =
                false;


            button.innerHTML =
                originalHtml;
        }
    }


    /*
     * =========================================================
     * MOBILE DETAIL
     * =========================================================
     */

    function toggleMobileDetail(
        button
    ) {

        const number =
            button.dataset.mobileDetail;


        if (!number) {
            return;
        }


        const detail =
            panel.querySelector(
                `[data-mobile-detail-row="${number}"]`
            );


        if (!detail) {
            return;
        }


        const isHidden =
            detail.classList.contains(
                'hidden'
            );


        if (isHidden) {

            /*
             * Buka detail.
             */
            detail.classList.remove(
                'hidden'
            );


            button.setAttribute(
                'aria-expanded',
                'true'
            );


            button.classList.add(
                'bg-amber-100',
                'dark:bg-amber-400/10'
            );


        } else {

            /*
             * Tutup detail.
             */
            detail.classList.add(
                'hidden'
            );


            button.setAttribute(
                'aria-expanded',
                'false'
            );


            button.classList.remove(
                'bg-amber-100',
                'dark:bg-amber-400/10'
            );
        }
    }


    /*
     * =========================================================
     * ESCAPE HTML
     * =========================================================
     */

    function escapeHtml(
        value
    ) {

        return String(value)
            .replaceAll(
                '&',
                '&amp;'
            )
            .replaceAll(
                '<',
                '&lt;'
            )
            .replaceAll(
                '>',
                '&gt;'
            )
            .replaceAll(
                '"',
                '&quot;'
            )
            .replaceAll(
                "'",
                '&#039;'
            );
    }


    /*
     * =========================================================
     * SEARCH EVENT
     * =========================================================
     */

    if (searchForm) {

        searchForm.addEventListener(
            'submit',
            async (event) => {

                event.preventDefault();


                const keyword =
                    searchInput
                        ?.value
                        ?.trim()
                        ?.toUpperCase() ||
                    '';


                if (!keyword) {

                    showMessage(
                        'Nomor container wajib diisi.',
                        'warning'
                    );


                    searchInput?.focus();

                    return;
                }


                await searchContainer(
                    keyword
                );
            }
        );
    }


    /*
     * =========================================================
     * DELEGATED CLICK
     * =========================================================
     */

    panel.addEventListener(
        'click',
        async (event) => {

            /*
             * -----------------------------------------------------
             * MOBILE DETAIL (!)
             * -----------------------------------------------------
             */

            const detailButton =
                event.target.closest(
                    '[data-mobile-detail]'
                );


            if (detailButton) {

                toggleMobileDetail(
                    detailButton
                );

                return;
            }


            /*
             * -----------------------------------------------------
             * PAGINATION
             * -----------------------------------------------------
             */

            const pageButton =
                event.target.closest(
                    '[data-hold-page]'
                );


            if (pageButton) {

                if (
                    pageButton.disabled ||
                    pageButton.hasAttribute(
                        'disabled'
                    )
                ) {
                    return;
                }


                const page =
                    parseInt(
                        pageButton.dataset.holdPage,
                        10
                    );


                if (
                    Number.isNaN(page) ||
                    page < 1
                ) {
                    return;
                }


                await loadHeldContainers(
                    page
                );

                return;
            }


            /*
             * -----------------------------------------------------
             * RELEASE
             * -----------------------------------------------------
             */

            const releaseButton =
                event.target.closest(
                    '[data-release-row]'
                );


            if (releaseButton) {

                await releaseContainer(
                    releaseButton
                );

                return;
            }


            /*
             * -----------------------------------------------------
             * RETRY
             * -----------------------------------------------------
             */

            const retryButton =
                event.target.closest(
                    '[data-retry-hold]'
                );


            if (retryButton) {

                await loadHeldContainers(
                    1
                );
            }
        }
    );


    /*
     * =========================================================
     * INITIAL LOAD
     * =========================================================
     */

    loadHeldContainers(1);
}