document.addEventListener("DOMContentLoaded", () => {
    const dropdown = document.getElementById("account-dropdown");
    const btn = document.getElementById("account-btn");
    const menu = document.getElementById("account-menu");

    if (!dropdown || !btn || !menu) return;

    let timeout: number | undefined;

    const openMenu = () => {
        menu.classList.remove("hidden");
        menu.classList.add("block");
        if (timeout) clearTimeout(timeout);
    };

    const closeMenu = () => {
        timeout = window.setTimeout(() => {
            menu.classList.add("hidden");
            menu.classList.remove("block");
        }, 200); // задержка, чтобы не исчезало сразу
    };

    btn.addEventListener("mouseenter", openMenu);
    dropdown.addEventListener("mouseleave", closeMenu);
    menu.addEventListener("mouseenter", openMenu);
    menu.addEventListener("mouseleave", closeMenu);
});
