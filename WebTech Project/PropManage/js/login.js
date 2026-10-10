const loginForm = document.getElementById("loginForm");

const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");

const emailError = document.getElementById("emailError");
const passwordError = document.getElementById("passwordError");

const passwordToggle =
    document.getElementById("passwordToggle");

const eyeIcon =
    document.getElementById("eyeIcon");

passwordToggle.addEventListener("click", function () {

    if (passwordInput.type === "password") {

        passwordInput.type = "text";

        eyeIcon.classList.remove("fa-eye");

        eyeIcon.classList.add("fa-eye-slash");

    } else {

        passwordInput.type = "password";

        eyeIcon.classList.remove("fa-eye-slash");

        eyeIcon.classList.add("fa-eye");

    }

});

loginForm.addEventListener("signin-button", function (event) {

    event.preventDefault();

    let isValid = true;

    emailError.textContent = "";
    passwordError.textContent = "";

    if (emailInput.value.trim() === "") {

        emailError.textContent =
            "Email Address/User ID is required.";

        isValid = false;
    }

    if (passwordInput.value.trim() === "") {

        passwordError.textContent =
            "Password is required.";

        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
    }

});

document.querySelectorAll(".demo-role-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        const role = btn.dataset.role;
        if (role === "admin") {
            emailInput.value = "admin@propmanage.com";
            passwordInput.value = "password123";
        } else if (role === "tenant") {
            emailInput.value = "tenant@propmanage.com";
            passwordInput.value = "password123";
        } else {
            emailInput.value = "landlord@propmanage.com";
            passwordInput.value = "password123";
        }
        loginForm.submit();
    });
});
