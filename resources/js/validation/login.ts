import { showError } from "./helpers";
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("loginForm") as HTMLFormElement;
    if (!form) return;

    form.addEventListener("submit", (e) => {
        let valid = true;

        // Clear old errors
        document.querySelectorAll(".input-error").forEach(el => el.textContent = "");
        document.querySelectorAll(".input").forEach(el => el.classList.remove("error"));

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

        if (!valid) {
            e.preventDefault();
        }
    });
});


