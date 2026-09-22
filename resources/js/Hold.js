(function () {
    'use strict';

    let initializedPanel = null;


    /*
    |--------------------------------------------------------------------------
    | PANEL
    |--------------------------------------------------------------------------
    */

    function getPanel() {
        return document.querySelector(
            '[data-panel-name="hold"]'
        );
    }


    function getCsrfToken() {
        const panel = getPanel();

        return panel?.dataset.csrfToken || '';
    }


    function getUrl(name) {
        const panel = getPanel();

        return panel?.dataset[name] || '';
    }


    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */

    function getContent() {
        const panel = getPanel();

        return panel?.querySelector(
            '[data-rows]'
        ) || null;
    }


    function getMessage() {
        const panel = getPanel();

        return panel?.querySelector(
            '[data-message]'
        ) || null;
    }


    /*
    |--------------------------------------------------------------------------
    | MESSAGE
    |--------------------------------------------------------------------------
    */

    function showMessage(
        message,
        type = 'danger'
    ) {
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
                'text-emerald-700',
                'dark:border-emerald-900/40',
                'dark:bg-emerald-900/20',
                'dark:text-emerald-300'
            );

        } else if (type === 'warning') {

            element.classList.add(
                'border-amber-200',
                'bg-amber-50',
                'text-amber-700',
                'dark:border-amber-900/40',
                'dark:bg-amber-900/20',
                'dark:text-amber-300'
            );

        } else {

            element.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700',
                'dark:border-red-900/40',
                'dark:bg-red-900/20',
                'dark:text-red-300'
            );
        }


        element.textContent =
            message;

        element.classList.remove(
            'hidden'
        );
    }


    function hideMessage() {
        const element = getMessage();

        if (!element) {
            return;
        }

        element.textContent = '';

        element.classList.add(
            'hidden'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH BUTTON
    |--------------------------------------------------------------------------
    */

    function setLoading(
        button,
        loading
    ) {
        if (!button) {
            return;
        }


        if (loading) {

            button.disabled = true;

            button.dataset.originalText =
                button.textContent;

            button.textContent =
                'SEARCHING...';

            return;
        }


        button.disabled = false;

        button.textContent =
            button.dataset.originalText ||
            'Search';
    }


    /*
    |--------------------------------------------------------------------------
    | POST JSON
    |--------------------------------------------------------------------------
    */

    async function postJson(
        url,
        payload
    ) {

        if (!url) {
            throw new Error(
                'URL request HOLD tidak ditemukan.'
            );
        }


        const response =
            await fetch(
                url,
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json',

                        'X-CSRF-TOKEN':
                            getCsrfToken(),

                        'X-Requested-With':
                            'XMLHttpRequest',
                    },

                    body:
                        JSON.stringify(
                            payload
                        ),
                }
            );


        let data = {};


        try {

            data =
                await response.json();

        } catch (error) {

            throw new Error(
                'Response server tidak dapat diproses.'
            );
        }


        if (!response.ok) {

            const error =
                new Error(
                    data.message ||
                    'Terjadi kesalahan pada server.'
                );

            error.status =
                response.status;

            error.response =
                data;

            throw error;
        }


        return data;
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    async function searchContainer(
        noCont
    ) {

        const content =
            getContent();


        if (!content) {

            showMessage(
                'Area hasil pencarian HOLD tidak ditemukan.',
                'danger'
            );

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

            const data =
                await postJson(
                    getUrl('searchUrl'),
                    {
                        search_cont:
                            noCont,
                    }
                );


            /*
            |--------------------------------------------------------------------------
            | Controller langsung mengembalikan FORM
            |--------------------------------------------------------------------------
            */

            content.innerHTML =
                data.html || '';


        } catch (error) {

            content.innerHTML =
                '';


            showMessage(
                error.message ||
                'Terjadi kesalahan saat mencari NO CONTAINER.',
                'danger'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD DATA HOLD
    |--------------------------------------------------------------------------
    */

    async function loadHeldContainers() {

        const panel =
            getPanel();


        if (!panel) {
            return;
        }


        const container =
            panel.querySelector(
                '[data-hold-list]'
            );


        if (!container) {
            return;
        }


        const url =
            getUrl('dataUrl');


        if (!url) {
            return;
        }


        try {

            const response =
                await fetch(
                    url,
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


            container.innerHTML =
                data.html || '';


        } catch (error) {

            container.innerHTML = `
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300">
                    ${escapeHtml(
                        error.message ||
                        'Gagal mengambil data container HOLD.'
                    )}
                </div>
            `;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | HOLD
    |--------------------------------------------------------------------------
    */

    async function handleHoldSubmit(
        form
    ) {

        const button =
            form.querySelector(
                '[data-hold-submit]'
            );


        const formData =
            new FormData(form);


        const warna =
            formData.get('warna');


        if (!warna) {

            showMessage(
                'Silakan pilih warna segel terlebih dahulu.',
                'warning'
            );

            return;
        }


        if (button) {

            button.disabled = true;

            button.textContent =
                'MEMPROSES...';
        }


        try {

            const response =
                await fetch(
                    form.action,
                    {
                        method: 'POST',

                        headers: {
                            'Accept':
                                'application/json',

                            'X-CSRF-TOKEN':
                                getCsrfToken(),

                            'X-Requested-With':
                                'XMLHttpRequest',
                        },

                        body:
                            formData,
                    }
                );


            const data =
                await response.json();


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


        } catch (error) {

            showMessage(
                error.message ||
                'Terjadi kesalahan saat proses HOLD.',
                'danger'
            );


        } finally {

            if (button) {

                button.disabled = false;

                button.textContent =
                    'HOLD';
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | RELEASE
    |--------------------------------------------------------------------------
    */

    async function handleRelease(
        button
    ) {

        const url =
            getUrl('releaseUrl');


        if (!url) {

            showMessage(
                'URL RELEASE tidak ditemukan.',
                'danger'
            );

            return;
        }


        const id =
            button.dataset.id || '';


        const noSpk =
            button.dataset.noSpk || '';


        const noCont =
            button.dataset.noCont || '';


        if (!id || !noCont) {

            showMessage(
                'Data container RELEASE tidak lengkap.',
                'danger'
            );

            return;
        }


        const originalText =
            button.textContent.trim();


        button.disabled = true;

        button.textContent =
            'MEMPROSES...';


        try {

            const response =
                await fetch(
                    url,
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'X-CSRF-TOKEN':
                                getCsrfToken(),

                            'X-Requested-With':
                                'XMLHttpRequest',
                        },

                        body:
                            JSON.stringify({
                                id: id,
                                nomercont: noCont,
                                nospk: noSpk,
                            }),
                    }
                );


            const data =
                await response.json();


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


        } catch (error) {

            showMessage(
                error.message ||
                'Terjadi kesalahan saat proses RELEASE.',
                'danger'
            );


        } finally {

            button.disabled = false;

            button.textContent =
                originalText || 'Release';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ESCAPE HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        const div =
            document.createElement(
                'div'
            );

        div.textContent =
            value ?? '';

        return div.innerHTML;
    }


    /*
    |--------------------------------------------------------------------------
    | INIT
    |--------------------------------------------------------------------------
    */

    function init() {

        const panel =
            getPanel();


        if (!panel) {
            return;
        }


        if (
            initializedPanel === panel
        ) {
            return;
        }


        initializedPanel =
            panel;


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        const searchForm =
            panel.querySelector(
                '[data-search-form]'
            );


        if (searchForm) {

            searchForm.addEventListener(
                'submit',
                async function (event) {

                    event.preventDefault();

                    event.stopPropagation();


                    const input =
                        searchForm.querySelector(
                            '[name="search_cont"]'
                        );


                    const button =
                        searchForm.querySelector(
                            '[data-search-button]'
                        );


                    const noCont =
                        (
                            input?.value || ''
                        )
                            .trim()
                            .toUpperCase();


                    if (!noCont) {

                        showMessage(
                            'NO CONTAINER wajib diisi.',
                            'warning'
                        );

                        input?.focus();

                        return;
                    }


                    if (input) {
                        input.value =
                            noCont;
                    }


                    setLoading(
                        button,
                        true
                    );


                    try {

                        await searchContainer(
                            noCont
                        );

                    } finally {

                        setLoading(
                            button,
                            false
                        );
                    }
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CLICK
        |--------------------------------------------------------------------------
        |
        | Hanya Release.
        | Tidak ada lagi data-hold-detail.
        |
        */

        panel.addEventListener(
            'click',
            function (event) {

                const releaseButton =
                    event.target.closest(
                        '[data-release-row]'
                    );


                if (!releaseButton) {
                    return;
                }


                event.preventDefault();


                handleRelease(
                    releaseButton
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | HOLD FORM
        |--------------------------------------------------------------------------
        */

        panel.addEventListener(
            'submit',
            function (event) {

                const holdForm =
                    event.target.closest(
                        '[data-hold-store-form]'
                    );


                if (!holdForm) {
                    return;
                }


                event.preventDefault();

                event.stopPropagation();


                handleHoldSubmit(
                    holdForm
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | LOAD TABLE HOLD
        |--------------------------------------------------------------------------
        */

        loadHeldContainers();
    }


    /*
    |--------------------------------------------------------------------------
    | INIT
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'DOMContentLoaded',
        init
    );


    document.addEventListener(
        'livewire:navigated',
        function () {

            initializedPanel =
                null;

            init();
        }
    );

})();