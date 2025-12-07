import { showError } from "./helpers";
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("registerForm") as HTMLFormElement;
    if (!form) return;

    form.addEventListener("submit", (e) => {
        let valid = true;

        // clear old errors
        document.querySelectorAll(".input-error").forEach(el => el.textContent = "");
        document.querySelectorAll(".input").forEach(el => el.classList.remove("error"));

        const name = form.querySelector("input[name='name']") as HTMLInputElement;
        const email = form.querySelector("input[name='email']") as HTMLInputElement;
        const password = form.querySelector("input[name='password']") as HTMLInputElement;

        // NAME
        if (name.value.trim().length < 2) {
            showError("name", "Name must have at least 2 characters.");
            name.classList.add("error");
            valid = false;
        }

        // EMAIL
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email.value.trim())) {
            showError("email", "Invalid email format.");
            email.classList.add("error");
            valid = false;
        }

        // PASSWORD
        if (password.value.length < 6) {
            showError("password", "Password must be at least 6 characters.");
            password.classList.add("error");
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
});


