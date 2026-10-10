<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | PropManage</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <div class="login-page">
        <div class="login-card">

            <div class="logo-box">
                <img src="../images/logo.png" alt="Logo">
            </div>


            <div class="login-heading">
                <h1>Reset Password</h1>
                <p>Enter your registered email address or User ID to receive a password reset link.</p>
            </div>


            <form id="forgotForm" action="#" method="POST">
                <div class="input-group">
                    <label for="resetEmail">Email Address / User ID</label>
                    <div class="input-box">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input type="text" id="resetEmail" name="email" placeholder="Enter Mail / ID" required>
                    </div>
                    <span class="error-message" id="resetEmailError"></span>
                </div>

                <button type="submit" class="signin-button" style="margin-top: 10px;">
                    Send Reset Link
                </button>
            </form>

            <div class="register-text" style="margin-top: 24px;">
                <span>Remembered your password?</span>
                <a href="login.php">Back to Sign In</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("forgotForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const email = document.getElementById("resetEmail").value.trim();
            if (!email) {
                document.getElementById("resetEmailError").textContent = "Please enter your email or User ID.";
                return;
            }
            alert("A password reset link has been dispatched to: " + email + "\nPlease check your inbox to proceed.");
            window.location.href = "login.php";
        });
    </script>
</body>
</html>
