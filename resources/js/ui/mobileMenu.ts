document.addEventListener("DOMContentLoaded", () => {
    const burger = document.getElementById("burger");
    const mobileMenu = document.getElementById("mobile-menu");

    if (!burger || !mobileMenu) return;

    burger.addEventListener("click", () => {
        burger.classList.toggle("active");
        mobileMenu.classList.toggle("hidden");
        mobileMenu.classList.toggle("block");
    });
});
