document.addEventListener("DOMContentLoaded", () => {
    const dropdown = document.getElementById("account-dropdown");
    const btn = document.getElementById("account-btn");
    const menu = document.getElementById("account-menu");

    // ak nieco z toho chyba, neriesime dropdown vobec
    if (!dropdown || !btn || !menu) return;

    // id timeoutu, aby sa dalo zrusit pri rychlom prejazde mysou
    let timeout: number | undefined;

    const openMenu = () => {
        // zobrazime menu cez tailwind classy
        menu.classList.remove("hidden");
        menu.classList.add("block");
        // ak je naplanovane zatvorenie, zrusime ho
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
