document.addEventListener("DOMContentLoaded", function () {
    const registerForm = document.getElementById("registerForm");
    const avatarError = document.getElementById("avatarError");
    const avatarInputs = document.querySelectorAll('input[name="avatar"]');

    registerForm.addEventListener("submit", function (event) {
        const avatarSelected = document.querySelector('input[name="avatar"]:checked');

        if (!avatarSelected) {
            avatarError.style.display = "block";
            avatarError.innerHTML = "⚠ Please choose an avatar.";
            avatarError.style.color = "red";
            event.preventDefault(); // Prevent form submission
        } else {
            avatarError.style.display = "none";
        }
    });

    // Show error message immediately when clicking "Submit" without selecting an avatar
    registerForm.querySelector("button[type='submit']").addEventListener("click", function () {
        const avatarSelected = document.querySelector('input[name="avatar"]:checked');
        if (!avatarSelected) {
            avatarError.style.display = "block";
            avatarError.innerHTML = "⚠ Please choose an avatar.";
            avatarError.style.color = "red";
        }
    });

    // Hide error when an avatar is selected
    avatarInputs.forEach(input => {
        input.addEventListener("change", function () {
            avatarError.style.display = "none";
        });
    });
});
