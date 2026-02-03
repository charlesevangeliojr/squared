    // Function to save login data if "Remember Me" is checked
    function saveLoginData(event) {
        event.preventDefault(); // Prevent default form submission

        let studentId = document.getElementById("studentId").value;
        let password = document.getElementById("password").value;
        let rememberMe = document.getElementById("rememberMe").checked;

        if (rememberMe) {
            document.cookie = `student_id=${studentId}; path=/; max-age=604800`; // 7 days
            document.cookie = `password=${password}; path=/; max-age=604800`; // 7 days
        } else {
            document.cookie = `student_id=; path=/; max-age=0`; // Delete cookie
            document.cookie = `password=; path=/; max-age=0`; // Delete cookie
        }

        // Submit the form manually after setting cookies
        document.getElementById("loginForm").submit();
    }

    // Function to load saved login data
    function loadLoginData() {
        let cookies = document.cookie.split("; ");
        let studentId = "";
        let password = "";

        cookies.forEach(cookie => {
            let [name, value] = cookie.split("=");
            if (name === "student_id") studentId = value;
            if (name === "password") password = value;
        });

        if (studentId && password) {
            document.getElementById("studentId").value = studentId;
            document.getElementById("password").value = password;
            document.getElementById("rememberMe").checked = true;
        }
    }

    // Load saved credentials when the page loads
    window.onload = loadLoginData;