(($) => {
    if (!$) {
        return;
    }

    const getPanel = () =>
        $(
            '[data-panel][data-panel-name="pickup"], [data-pickup-panel]',
        ).first();
    const hasPanel = () => getPanel().length > 0;

    const getSpkInput = () => $("#panel-no-spk, #pickup-no-spk", getPanel());
    const getSearchForm = () =>
        $("[data-search-form], [data-pickup-search-form]", getPanel());
    const getSearchButton = () =>
        $("[data-search-button], [data-pickup-search-button]", getPanel());
    const getResultWrapper = () =>
        $("[data-result], [data-pickup-result]", getPanel());
    const getResultSpk = () =>
        $("[data-result-spk], [data-pickup-result-spk]", getPanel());
    const getRowsContainer = () =>
        $("[data-rows], [data-pickup-rows]", getPanel());

    const getSearchUrl = () =>
        getPanel().data("searchUrl") || getPanel().data("pickupSearchUrl");
    const getStoreUrl = () =>
        getPanel().data("storeUrl") || getPanel().data("pickupStoreUrl");
    const getSendUrl = () =>
        getPanel().data("sendUrl") || getPanel().data("pickupSendUrl");
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

    const reloadPickupList = (onSuccess, onError) => {
        const noSpk = getSpkInput().val()?.trim();
        const searchUrl = getSearchUrl();
        const token = getCsrfToken();

        if (!hasPanel() || !noSpk || !searchUrl) {
            return;
        }

        $.ajax({
            url: searchUrl,
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": token,
            },
            data: {
                no_spk: noSpk,
                _token: token,
            },
            success: (response) => {
                getResultSpk().text(noSpk);
                getRowsContainer()
                    .empty()
                    .html(response.data || "");
                getResultWrapper().removeClass("hidden");
                if (typeof onSuccess === "function") {
                    onSuccess(response);
                }
            },
            error: (xhr) => {
                if (typeof onError === "function") {
                    onError(xhr);
                }
            },
        });
    };

    const initPickupPanel = () => {
        if (hasPanel()) {
            $("[data-handheld-menu]").addClass("hidden");
            getSpkInput().trigger("focus");
        }
    };

    const bindPickupHandlers = () => {
        $(document).off("bos:reload-data.bosPickup");
        $(document).on("bos:reload-data.bosPickup", () => {
            const noSpk = getSpkInput().val()?.trim();
            if (hasPanel() && noSpk) {
                reloadPickupList();
            }
        });

        $(document).off(
            "submit.bosPickupSearch",
            "[data-search-form], [data-pickup-search-form]",
        );
        $(document).on(
            "submit.bosPickupSearch",
            "[data-search-form], [data-pickup-search-form]",
            function (event) {
                if (!hasPanel()) return;

                event.preventDefault();

                const noSpk = getSpkInput().val()?.trim();
                const $button = getSearchButton();
                const defaultLabel =
                    $button.data("default-label") || $button.html();
                const searchUrl = getSearchUrl();

                $button.data("default-label", defaultLabel);

                if (!noSpk) {
                    showAlert(
                        "warning",
                        "Nomor SPK kosong",
                        "Silakan isi nomor SPK terlebih dahulu.",
                    );
                    return;
                }

                if (!searchUrl) {
                    showAlert(
                        "error",
                        "Konfigurasi belum lengkap",
                        "URL pencarian pickup tidak ditemukan.",
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
                        no_spk: noSpk,
                        _token: token,
                    },
                    success: (response) => {
                        getResultSpk().text(noSpk);
                        getRowsContainer()
                            .empty()
                            .html(response.data || "");
                        getResultWrapper().removeClass("hidden");
                    },
                    error: (xhr) => {
                        getResultWrapper().addClass("hidden");
                        showAlert(
                            "info",
                            "SPK tidak ditemukan",
                            xhr.responseJSON?.message ||
                                "Data SPK tidak ditemukan.",
                        );
                    },
                    complete: () => {
                        setButtonLoading($button, false, defaultLabel);
                    },
                });
            },
        );

        $(document).off(
            "submit.bosPickupStore",
            "[data-store-form], [data-pickup-store-form]",
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
                        "error",
                        "Konfigurasi belum lengkap",
                        "URL simpan pickup tidak ditemukan.",
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
                        showToast(
                            "success",
                            "Berhasil",
                            response.message || "Data Container berhasil di pickup.",
                        );

                    },
                    error: (xhr) => {
                        showAlert(
                            "error",
                            "Gagal",
                            xhr.responseJSON?.message ||
                                "Data container gagal dipickup.",
                        );
                    },
                    complete: () => {
                        const btnText = $button.text().trim();
                        if (btnText === "Pickup Container") {
                            $button.prop("disabled", false);
                        } else if (btnText !== "Terkirim") {
                            setButtonLoading($button, false, defaultLabel);
                        }
                    },
                });
            },
        );

        $(document).on(
            "submit.bosPickupSend",
            "[data-send-form], [data-pickup-send-form]",
            function (event) {
                if (!hasPanel()) return;

                event.preventDefault();

                const $form = $(this);
                const $button = $form.find('button[type="submit"]');
                const defaultLabel =
                    $button.data("default-label") || $button.html();
                const sendUrl = getSendUrl();

                $button.data("default-label", defaultLabel);

                if (!sendUrl) {
                    showAlert(
                        "error",
                        "Konfigurasi belum lengkap",
                        "URL simpan pickup tidak ditemukan.",
                    );
                    return;
                }

                setButtonLoading($button, true, defaultLabel);

                const token = getCsrfToken();

                $.ajax({
                    url: sendUrl,
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": token,
                    },
                    data: $form.serialize(),
                    success: (response) => {
                        showToast(
                            "success",
                            "Berhasil",
                            response.message || "Data pickup berhasil dikirim.",
                        );

                        // Update button to "Pickup Container"
                        $button
                            .text("Pickup Container")
                            .removeClass(
                                "bg-emerald-600 hover:bg-emerald-700 bg-slate-500",
                            )
                            .addClass("bg-sky-600 hover:bg-sky-700");
                        $button.data("default-label", "Pickup Container");

                        // Update form attribute from send-form to store-form
                        $form
                            .removeAttr("data-send-form")
                            .removeAttr("data-pickup-send-form")
                            .attr("data-store-form", "");

                        // Reload table list without reloading page
                        reloadPickupList();
                    },
                    error: (xhr) => {
                        showAlert(
                            "error",
                            "Gagal",
                            xhr.responseJSON?.message ||
                                "Data pickup gagal dikirim.",
                        );
                    },
                    complete: () => {
                        const btnText = $button.text().trim();
                        if (btnText === "Pickup Container") {
                            $button.prop("disabled", false);
                        } else if (btnText !== "Terkirim") {
                            setButtonLoading($button, false, defaultLabel);
                        }
                    },
                });
            },
        );

        $(document).off(
            "submit.bosPickupStore",
            "[data-store-form], [data-pickup-store-form]",
        );
        $(document).on(
            "submit.bosPickupStore",
            "[data-store-form], [data-pickup-store-form]",
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
                        "error",
                        "Konfigurasi belum lengkap",
                        "URL simpan pickup tidak ditemukan.",
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
                            response.message || "Data pickup berhasil dikirim.",
                        );
                        $button
                            .text("Terkirim")
                            .removeClass(
                                "bg-emerald-600 hover:bg-emerald-700 bg-sky-600 hover:bg-sky-700",
                            )
                            .addClass("bg-slate-500");
                    },
                    error: (xhr) => {
                        showAlert(
                            "error",
                            "Gagal",
                            xhr.responseJSON?.message ||
                                "Data pickup gagal dikirim.",
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
    bindPickupHandlers();
    initPickupPanel();

    $(() => initPickupPanel());
    $(document).on("livewire:navigated", () => {
        bindPickupHandlers();
        initPickupPanel();
    });
})(window.jQuery);
