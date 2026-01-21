document.addEventListener("DOMContentLoaded", () => {
    const burger = document.getElementById("burger");
    const mobileMenu = document.getElementById("mobile-menu");

    // ak nie je burger alebo menu v DOM, nic nerobime
    if (!burger || !mobileMenu) return;

    burger.addEventListener("click", () => {
        // toggluje CSS animaciu hamburgera
        burger.classList.toggle("active");
        // show/hide mobilneho menu cez tailwind classy
        mobileMenu.classList.toggle("hidden");
        mobileMenu.classList.toggle("block");
    });
});
