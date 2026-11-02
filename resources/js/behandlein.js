(($) => {
    if (!$) {
        return;
    }

    const getPanel = () =>
        $('[data-panel][data-panel-name="behandlein"]').first();
    const hasPanel = () => getPanel().length > 0;

    const getNoContInput = () => $("#panel-no-container", getPanel());
    const getSearchForm = () => $("[data-search-form]", getPanel());
    const getSearchButton = () => $("[data-search-button]", getPanel());
    const getResultWrapper = () => $("[data-result]", getPanel());
    const getResultContainer = () => $("[data-result-container]", getPanel());
    const getRowsContainer = () => $("[data-rows]", getPanel());

    const getSearchUrl = () => getPanel().data("searchUrl");
    const getStoreUrl = () => getPanel().data("storeUrl");
    const getCsrfToken = () => {
        return (
            getPanel().attr("data-csrf-token") ||
            getPanel().data("csrfToken") ||
            (typeof window.getCsrfToken === "function"
                ? window.getCsrfToken()
                : "") ||
            $('meta[name="csrf-token"]').attr("content") ||
            ""
        );
    };

    const showAlert = (icon, title, text) => {
        if (typeof window.showAlert === "function") {
            window.showAlert(icon, title, text);
            return;
        }

        if (window.Swal) {
            Swal.fire({
                icon,
                title,
                text,
                confirmButtonText: "Mengerti",
                confirmButtonColor: "#0369a1",
            });
            return;
        }

        alert(text);
    };

    const showToast = (icon, title, text = "") => {
        if (typeof window.showToast === "function") {
            window.showToast(icon, title, text);
            return;
        }

        if (window.Swal) {
            Swal.fire({
                toast: true,
                position: "top-end",
                icon,
                title: title || text,
                text: title ? text : "",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            return;
        }

        alert(text || title);
    };

    const setButtonLoading = ($button, isLoading, label) => {
        if (typeof window.setButtonLoading === "function") {
            window.setButtonLoading($button, isLoading, label);
            return;
        }

        $button.prop("disabled", isLoading);
        $button.html(
            isLoading
                ? '<span class="inline-block size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Memproses'
                : label,
        );
    };

    const initBehandleInPanel = () => {
        if (hasPanel()) {
            $("[data-handheld-menu]").addClass("hidden");
            getNoContInput().trigger("focus");
            $("[data-search-form]", getPanel()).trigger("reset");
            $("[data-rows]", getPanel()).empty();
            $("[data-result]", getPanel()).addClass("hidden");
        }
    };

    const reloadbehandleInPanelAfterInsert = (forcePageReload = false) => {
        if (forcePageReload) {
            window.location.reload();
            return;
        }

        const no_cont = getNoContInput().val()?.trim();
        const searchUrl = getSearchUrl();
        const token = getCsrfToken();

        if (!hasPanel() || !no_cont || !searchUrl) {
            return;
        }

        $.ajax({
            url: searchUrl,
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": token,
            },
            data: {
                no_cont: no_cont,
                _token: token,
            },
            success: (response) => {
                getResultContainer().text(no_cont);
                getRowsContainer()
                    .empty()
                    .html(response.data || "");
                getResultWrapper().removeClass("hidden");
            },
            error: (xhr) => {
                // If 404 (all containers on this SPK have finished pickup)
                if (xhr.status === 404) {
                    getResultWrapper().addClass("hidden");
                    getRowsContainer().empty();
                    getNoContInput().val("").trigger("focus");
                    showToast(
                        "success",
                        "Selesai",
                        "Data container telah selesai diproses. Silakan masukkan nomor container berikutnya.",
                    );
                }
            },
        });
    };

    const bindBehandleInHandlers = () => {
        $(document).off("bos:reload-data.bosBehandleIn");
        $(document).on("bos:reload-data.bosBehandleIn", () => {
            const noCont = getNoContInput().val()?.trim();
            if (hasPanel() && noCont) {
                getSearchForm().trigger("submit");
            }
        });

        $(document).off("click.bosPickupReload", "[data-reload-behandlein]");
        $(document).on(
            "click.bosPickupReload",
            "[data-reload-behandlein]",
            function () {
                if (!hasPanel()) return;
                const no_cont = getNoContInput().val()?.trim();
                if (no_cont) {
                    reloadbehandleInPanelAfterInsert();
                } else {
                    window.location.reload();
                }
            },
        );

        $(document).off("submit.bosBehandleInSearch", "[data-search-form]");
        $(document).on(
            "submit.bosBehandleInSearch",
            "[data-search-form]",
            function (event) {
                if (!hasPanel()) return;

                event.preventDefault();

                const noCont = getNoContInput().val()?.trim();
                const $button = getSearchButton();
                const defaultLabel =
                    $button.data("default-label") || $button.html();
                const searchUrl = getSearchUrl();

                $button.data("default-label", defaultLabel);

                if (!noCont) {
                    showAlert(
                        "warning",
                        "Nomor Container kosong",
                        "Silakan isi nomor Container terlebih dahulu.",
                    );
                    return;
                }

                if (!searchUrl) {
                    showAlert(
                        "info",
                        "Dalam Pengembangan",
                        "Fitur pencarian Behandle In sedang dalam pengembangan.",
                    );
                    return;
                }

                setButtonLoading($button, true, defaultLabel);

                const token = getCsrfToken();

                $.ajax({
                    url: searchUrl,
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": token,
                    },
                    data: {
                        no_cont: noCont,
                        _token: token,
                    },
                    success: (response) => {
                        getResultContainer().text(noCont);
                        getRowsContainer()
                            .empty()
                            .html(response.data || "");
                        getResultWrapper().removeClass("hidden");
                    },
                    error: (xhr) => {
                        getResultWrapper().addClass("hidden");
                        let alertType = xhr.status >= 500 ? "error" : "info";
                        let alertTitle =
                            xhr.status >= 500
                                ? "Terjadi Kesalahan"
                                : "Container tidak ditemukan";
                        showAlert(
                            alertType,
                            alertTitle,
                            xhr.responseJSON?.message ||
                                "Gagal mengambil data container, harap hubungi tim IT",
                        );
                    },
                    complete: () => {
                        setButtonLoading($button, false, defaultLabel);
                    },
                });
            },
        );

        $(document).off("submit.bosBehandleInStore", "[data-store-form]");
        $(document).on(
            "submit.bosBehandleInStore",
            "[data-store-form]",
            function (event) {
                if (!hasPanel()) return;

                event.preventDefault();

                const $form = $(this);
                const $button = $form.find('button[type="submit"]');
                const defaultLabel =
                    $button.data("default-label") || $button.html();
                const storeUrl = getStoreUrl();

                $button.data("default-label", defaultLabel);

                if (!storeUrl) {
                    showAlert(
                        "info",
                        "Dalam Pengembangan",
                        "Fitur simpan Behandle In sedang dalam pengembangan.",
                    );
                    return;
                }

                setButtonLoading($button, true, defaultLabel);

                const token = getCsrfToken();

                $.ajax({
                    url: storeUrl,
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": token,
                    },
                    data: $form.serialize(),
                    success: (response) => {
                        showAlert(
                            "success",
                            "Berhasil",
                            response.message ||
                                "Data Container berhasil diproses Behandle In.",
                            () => {
                                bindBehandleInHandlers();
                                initBehandleInPanel();
                            },
                        );
                        $button
                            .text("Terkirim")
                            .removeClass("bg-emerald-600 hover:bg-emerald-700")
                            .addClass("bg-slate-500");

                        bindBehandleInHandlers();
                        initBehandleInPanel();
                    },
                    error: (xhr) => {
                        showAlert(
                            "error",
                            "Gagal",
                            xhr.responseJSON?.message ||
                                "Data Behandle In gagal dikirim.",
                        );
                    },
                    complete: () => {
                        if ($button.text().trim() !== "Terkirim") {
                            setButtonLoading($button, false, defaultLabel);
                        }
                    },
                });
            },
        );
    };

    // Execute
    bindBehandleInHandlers();
    initBehandleInPanel();

    $(() => initBehandleInPanel());
    $(document).on("livewire:navigated", () => {
        bindBehandleInHandlers();
        initBehandleInPanel();
    });
})(window.jQuery);
