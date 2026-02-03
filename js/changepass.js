document.addEventListener("DOMContentLoaded", function () {
    const newPassword = document.getElementById("newPassword");
    const confirmPassword = document.getElementById("confirmPassword");
    const matchMessage = document.getElementById("matchMessage");

    // Validate password match in real-time
    function validatePasswordMatch() {
        if (confirmPassword.value.length > 0) {
            if (newPassword.value !== confirmPassword.value) {
                matchMessage.textContent = "⚠ Passwords do not match.";
                matchMessage.style.display = "block";
                matchMessage.style.color = "red";
                confirmPassword.classList.add("is-invalid");
                confirmPassword.classList.remove("is-valid");
            } else {
                matchMessage.textContent = "";
                matchMessage.style.display = "none";
                confirmPassword.classList.remove("is-invalid");
                confirmPassword.classList.add("is-valid");
            }
        } else {
            matchMessage.style.display = "none";
            confirmPassword.classList.remove("is-invalid", "is-valid");
        }
    }

    newPassword.addEventListener("input", validatePasswordMatch);
    confirmPassword.addEventListener("input", validatePasswordMatch);

    // Toggle password visibility
    function togglePassword(inputId, toggleIconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(toggleIconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("bi-eye", "bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("bi-eye-slash", "bi-eye");
        }
    }

    // Expose function globally
    window.togglePassword = togglePassword;
});