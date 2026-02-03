document.addEventListener("DOMContentLoaded", function () {
    const password = document.getElementById("registerPassword");
    const confirmps = document.getElementById("registerConfirmps");
    const matchMessage = document.getElementById("matchMessage");
    const registerForm = document.querySelector("#registerModal form");
    const avatarError = document.getElementById("avatarError");

    // Function to validate password match in real-time
    function validatePasswordMatch() {
        if (confirmps.value.length > 0) {
            if (password.value !== confirmps.value) {
                matchMessage.textContent = "⚠ Passwords do not match.";
                matchMessage.style.display = "block";
                matchMessage.style.color = "red";
                confirmps.classList.add("is-invalid");
                confirmps.classList.remove("is-valid");
            } else {
                matchMessage.textContent = "";
                matchMessage.style.display = "block";
                matchMessage.style.color = "green";
                confirmps.classList.remove("is-invalid");
                confirmps.classList.add("is-valid");
            }
        } else {
            matchMessage.style.display = "none";
            confirmps.classList.remove("is-invalid", "is-valid");
        }
    }

    // Attach real-time validation on input change
    password.addEventListener("input", validatePasswordMatch);
    confirmps.addEventListener("input", validatePasswordMatch);

    // Prevent form submission if passwords don't match
    registerForm.addEventListener("submit", function (event) {
        if (password.value !== confirmps.value) {
            event.preventDefault();
            matchMessage.textContent = "⚠ Passwords do not match.";
            matchMessage.style.display = "block";
            matchMessage.style.color = "red";
            confirmps.classList.add("is-invalid");
        }
    });

    // Function to toggle password visibility
    function togglePassword(inputId, toggleIconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(toggleIconId);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }

    // Expose function globally
    window.togglePassword = togglePassword;
});