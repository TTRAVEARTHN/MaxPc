export function showError(field: string, message: string) {
    const el = document.querySelector(`[data-error="${field}"]`);
    if (el) el.textContent = message;
}
