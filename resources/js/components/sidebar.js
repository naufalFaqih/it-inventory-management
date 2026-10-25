document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    const toggleButton = document.getElementById("sidebar-toggle");
    const backdrop = document.getElementById("sidebar-backdrop");

    if (!sidebar || !toggleButton || !backdrop) {
        return;
    }

    const openSidebar = () => {
        sidebar.classList.remove("-translate-x-full");
        sidebar.classList.add("translate-x-0");
        backdrop.classList.remove("hidden");
    };

    const closeSidebar = () => {
        sidebar.classList.add("-translate-x-full");
        sidebar.classList.remove("translate-x-0");
        backdrop.classList.add("hidden");
    };

    toggleButton.addEventListener("click", () => {
        const isHidden = sidebar.classList.contains("-translate-x-full");

        if (isHidden) {
            openSidebar();
        } else {
            closeSidebar();
        }
    });

    backdrop.addEventListener("click", closeSidebar);

    window.addEventListener("resize", () => {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove("-translate-x-full");
            sidebar.classList.add("translate-x-0");
            backdrop.classList.add("hidden");
        }
    });
});
