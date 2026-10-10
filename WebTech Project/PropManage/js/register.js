
const registerForm =
    document.getElementById("registerForm");

const fullName =
    document.getElementById("fullName");

const email =
    document.getElementById("email");

const password =
    document.getElementById("password");

const confirmPassword =
    document.getElementById("confirmPassword");

const role =
    document.getElementById("role");

const terms =
    document.getElementById("terms");

const fullNameError =
    document.getElementById("fullNameError");

const emailError =
    document.getElementById("emailError");

const passwordError =
    document.getElementById("passwordError");

const confirmPasswordError =
    document.getElementById("confirmPasswordError");

const roleError =
    document.getElementById("roleError");

const termsError =
    document.getElementById("termsError");

const passwordToggle =
    document.getElementById("passwordToggle");

const passwordEye =
    document.getElementById("passwordEye");

passwordToggle.addEventListener("click", function () {

    if (password.type === "password") {

        password.type = "text";

        passwordEye.classList.remove(
            "fa-eye"
        );

        passwordEye.classList.add(
            "fa-eye-slash"
        );

    } else {

        password.type = "password";

        passwordEye.classList.remove(
            "fa-eye-slash"
        );

        passwordEye.classList.add(
            "fa-eye"
        );

    }

});

const confirmPasswordToggle =
    document.getElementById(
        "confirmPasswordToggle"
    );

const confirmPasswordEye =
    document.getElementById(
        "confirmPasswordEye"
    );

confirmPasswordToggle.addEventListener(
    "click",
    function () {

        if (
            confirmPassword.type ===
            "password"
        ) {

            confirmPassword.type = "text";

            confirmPasswordEye.classList.remove(
                "fa-eye"
            );

            confirmPasswordEye.classList.add(
                "fa-eye-slash"
            );

        } else {

            confirmPassword.type =
                "password";

            confirmPasswordEye.classList.remove(
                "fa-eye-slash"
            );

            confirmPasswordEye.classList.add(
                "fa-eye"
            );

        }

    }
);

registerForm.addEventListener(
    "signup-button",
    function (event) {

        event.preventDefault();

        fullNameError.textContent = "";
        emailError.textContent = "";
        passwordError.textContent = "";
        confirmPasswordError.textContent = "";
        roleError.textContent = "";
        termsError.textContent = "";

        let isValid = true;

        if (
            fullName.value.trim() === ""
        ) {

            fullNameError.textContent =
                "Full name is required.";

            isValid = false;

        }

        if (
            email.value.trim() === ""
        ) {

            emailError.textContent =
                "Email address is required.";

            isValid = false;

        } else if (
            !isValidEmail(
                email.value.trim()
            )
        ) {

            emailError.textContent =
                "Please enter a valid email.";

            isValid = false;

        }

        if (
            password.value === ""
        ) {

            passwordError.textContent =
                "Password is required.";

            isValid = false;

        } else if (
            password.value.length < 8
        ) {

            passwordError.textContent =
                "Password must contain at least 8 characters.";

            isValid = false;

        }

        if (
            confirmPassword.value === ""
        ) {

            confirmPasswordError.textContent =
                "Please confirm your password.";

            isValid = false;

        } else if (
            confirmPassword.value !==
            password.value
        ) {

            confirmPasswordError.textContent =
                "Passwords do not match.";

            isValid = false;

        }

        if (
            role.value === ""
        ) {

            roleError.textContent =
                "Please select a role.";

            isValid = false;

        }

        if (!terms.checked) {

            termsError.textContent =
                "You must agree to the Terms and Conditions.";

            isValid = false;

        }

        if (!isValid) {
            event.preventDefault();
        }

    }
);

function isValidEmail(value) {

    const pattern =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    return pattern.test(value);
}