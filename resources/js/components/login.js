const prefersReducedMotion = () => window.matchMedia("(prefers-reduced-motion: reduce)").matches;

function initPasswordToggle() {
    const input = document.getElementById("password");
    const toggle = document.querySelector("[data-password-toggle]");

    if (!input || !toggle) {
        return;
    }

    const iconShow = toggle.querySelector("[data-icon-show]");
    const iconHide = toggle.querySelector("[data-icon-hide]");

    toggle.addEventListener("click", () => {
        const shouldShow = input.type === "password";

        input.type = shouldShow ? "text" : "password";
        toggle.setAttribute("aria-pressed", String(shouldShow));
        toggle.setAttribute("aria-label", shouldShow ? "Sembunyikan kata sandi" : "Tampilkan kata sandi");

        iconShow?.classList.toggle("hidden", shouldShow);
        iconHide?.classList.toggle("hidden", !shouldShow);

        input.focus({ preventScroll: true });
        input.setSelectionRange(input.value.length, input.value.length);
    });
}

function initCapsLockHint() {
    const input = document.getElementById("password");
    const hint = document.querySelector("[data-caps-hint]");

    if (!input || !hint) {
        return;
    }

    const syncHint = (event) => {
        if (typeof event.getModifierState !== "function") {
            return;
        }

        const isOn = event.getModifierState("CapsLock");
        hint.style.display = isOn ? "" : "none";
    };

    ["keydown", "keyup"].forEach((type) => input.addEventListener(type, syncHint));
    input.addEventListener("blur", () => {
        hint.style.display = "none";
    });
}

function initSubmitState() {
    const form = document.querySelector("[data-login-form]");

    if (!form) {
        return;
    }

    const button = form.querySelector("[data-submit-button]");
    const label = form.querySelector("[data-submit-label]");
    const spinner = form.querySelector("[data-submit-spinner]");
    const arrow = form.querySelector("[data-submit-arrow]");

    if (!button) {
        return;
    }

    const defaultLabel = label ? label.textContent : "";

    const setLoading = (isLoading) => {
        button.disabled = isLoading;
        button.dataset.loading = String(isLoading);
        button.setAttribute("aria-busy", String(isLoading));

        if (label) {
            label.textContent = isLoading ? "Memproses…" : defaultLabel;
        }

        spinner?.classList.toggle("hidden", !isLoading);
        arrow?.classList.toggle("hidden", isLoading);
    };

    form.addEventListener("submit", (event) => {
        if (button.dataset.loading === "true") {
            event.preventDefault();

            return;
        }

        setLoading(true);
    });

    // Pulihkan tombol saat halaman diambil dari bfcache (tombol Back).
    window.addEventListener("pageshow", (event) => {
        if (event.persisted) {
            setLoading(false);
        }
    });
}

function initPointerGlow() {
    const host = document.querySelector("[data-login-glow-host]");
    const glow = document.querySelector("[data-login-glow]");

    if (!host || !glow) {
        return;
    }

    if (!window.matchMedia("(hover: hover) and (pointer: fine)").matches || prefersReducedMotion()) {
        return;
    }

    host.addEventListener("pointermove", (event) => {
        const rect = host.getBoundingClientRect();

        glow.style.setProperty("--login-glow-x", `${event.clientX - rect.left}px`);
        glow.style.setProperty("--login-glow-y", `${event.clientY - rect.top}px`);
        glow.style.opacity = "1";
    });

    host.addEventListener("pointerleave", () => {
        glow.style.opacity = "";
    });
}

document.addEventListener("DOMContentLoaded", () => {
    initPasswordToggle();
    initCapsLockHint();
    initSubmitState();
    initPointerGlow();
});
