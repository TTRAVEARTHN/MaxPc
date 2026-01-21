document.addEventListener('DOMContentLoaded', () => {
    const flashEls = document.querySelectorAll<HTMLElement>('[data-flash]');
    if (!flashEls.length) return;


    setTimeout(() => {
        flashEls.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(-4px)';
            setTimeout(() => el.remove(), 300);
        });
    }, 2000);
});
