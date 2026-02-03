document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault();
    
    let studentId = document.getElementById("studentId").value.trim();
    let password = document.getElementById("password").value.trim();
    let loginMessage = document.getElementById("loginMessage");

    // Clear previous message
    loginMessage.textContent = "";
    loginMessage.className = "";

    fetch("../php/login.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `student_id=${encodeURIComponent(studentId)}&password=${encodeURIComponent(password)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            loginMessage.className = "text-success"; // Success message styling
            loginMessage.textContent = "Login successful. Redirecting...";
            setTimeout(() => {
                window.location.href = "home.php"; // Redirect after a short delay
            }, 1000);
        } else {
            loginMessage.className = "text-danger"; // Error message styling
            loginMessage.textContent = data.message;
        }
    })
    .catch(error => {
        loginMessage.className = "text-danger";
        loginMessage.textContent = "An error occurred. Please try again.";
        console.error("Login Error:", error);
    });
});
