export function showError(field: string, message: string) {
    // hladanie elementu podla custom data-error atributu
    const el = document.querySelector(`[data-error="${field}"]`);
    // ochrana ak element neexistuje
    if (el) el.textContent = message;
}
