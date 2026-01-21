import { showError } from "./helpers";
document.addEventListener("DOMContentLoaded", () => {
    // explicitny cast na HTMLFormElement, aby TS vedel o vlastnostiach formu
    const form = document.getElementById("loginForm") as HTMLFormElement;
    // jednoduchy guard ak by formular nebol na stranke
    if (!form) return;

    form.addEventListener("submit", (e) => {
        let valid = true;

        // vycistenie starych chyb pred novou validaciou
        document.querySelectorAll(".input-error").forEach(el => el.textContent = "");
        document.querySelectorAll(".input").forEach(el => el.classList.remove("error"));

        // querySelector viazany na konkretny form, nie na cely dokument
        const email = form.querySelector("input[name='email']") as HTMLInputElement;
        const password = form.querySelector("input[name='password']") as HTMLInputElement;

        // EMAIL validation
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailPattern.test(email.value.trim())) {
            showError("email", "Please enter a valid email.");
            email.classList.add("error");
            valid = false;
        }

        // PASSWORD validation
        if (password.value.trim().length < 3) {
            showError("password", "Password must be at least 3 characters.");
            password.classList.add("error");
            valid = false;
        }

        // zablokujeme submit ak nieco nepreslo validaciou
        if (!valid) {
            e.preventDefault();
        }
    });
});


