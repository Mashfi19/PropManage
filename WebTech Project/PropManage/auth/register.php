<?php
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $role = trim($_POST['role'] ?? '');

    if (empty($fullName) || empty($email) || empty($password) || empty($role)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address format.';
    } else {
        if ($db_connected && $pdo) {
            try {

                $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email LIMIT 1");
                $checkStmt->execute([':email' => $email]);
                if ($checkStmt->fetch()) {
                    $error = 'An account with this email address already exists. Please sign in.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $pdo->beginTransaction();

                    $insertUser = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role, status) VALUES (:name, :email, :hash, :role, 'active')");
                    $insertUser->execute([
                        ':name' => $fullName,
                        ':email' => $email,
                        ':hash' => $hash,
                        ':role' => $role
                    ]);
                    $userId = $pdo->lastInsertId();

                    if ($role === 'landlord') {
                        $profileStmt = $pdo->prepare("INSERT INTO landlord_profiles (user_id) VALUES (:uid)");
                        $profileStmt->execute([':uid' => $userId]);
                    } elseif ($role === 'tenant') {
                        $profileStmt = $pdo->prepare("INSERT INTO tenant_profiles (user_id) VALUES (:uid)");
                        $profileStmt->execute([':uid' => $userId]);
                    }


                    $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action_type, details, ip_address) VALUES (:uid, 'User Registration', 'New account created successfully', :ip)");
                    $logStmt->execute([':uid' => $userId, ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);

                    $pdo->commit();

                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_name'] = $fullName;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['role'] = $role;

                    if ($role === 'tenant') {
                        header("Location: ../tenant/explore.php");
                    } else {
                        header("Location: ../landlord/dashboard.php");
                    }
                    exit();
                }
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error = 'Registration failed: ' . $e->getMessage();
            }
        } else {

            $_SESSION['user_name'] = $fullName;
            $_SESSION['user_email'] = $email;
            $_SESSION['role'] = $role;
            if ($role === 'tenant') {
                header("Location: ../tenant/explore.php");
            } else {
                header("Location: ../landlord/dashboard.php");
            }
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Register | PropManage</title>

    <link
        rel="stylesheet"
        href="../css/register.css"
    >


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body>


    <div class="register-page">


        <div class="register-card">


            <div class="logo-box">
                <img src="../images/logo.png" alt="Logo">
            </div>


            <div class="register-heading">

                <h1>
                    Create Account
                </h1>

                <p>
                    Join PropManage to streamline your property
                    <br>
                    management experience.
                </p>

            </div>


            <?php if (!empty($error)): ?>
                <div style="background: #fee7e7; color: #c91919; border-left: 4px solid #c91919; padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 18px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($db_connected): ?>
                <div style="background: #e7f8f1; color: #008f5f; font-size: 11px; padding: 5px 10px; border-radius: 6px; margin-bottom: 14px; display: flex; align-items: center; gap: 6px;">
                    <span>●</span> <strong>MySQL Online:</strong> Ready to register into `propmanage_db`
                </div>
            <?php endif; ?>


            <form
                id="registerForm"
                action="register.php"
                method="POST"
            >


                <div class="input-group">

                    <label for="fullName">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="fullName"
                        name="fullName"
                        placeholder="Enter Name"
                    >

                    <span
                        class="error-message"
                        id="fullNameError"
                    ></span>

                </div>


                <div class="input-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter Email"
                    >

                    <span
                        class="error-message"
                        id="emailError"
                    ></span>

                </div>


                <div class="input-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="password-box">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter Password"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                        >

                            <i
                                class="fa-regular fa-eye"
                                id="passwordEye"
                            ></i>

                        </button>

                    </div>

                    <span
                        class="error-message"
                        id="passwordError"
                    ></span>

                </div>


                <div class="input-group">

                    <label for="confirmPassword">
                        Confirm Password
                    </label>

                    <div class="password-box">

                        <input
                            type="password"
                            id="confirmPassword"
                            name="confirmPassword"
                            placeholder="Enter Confirm Password"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="confirmPasswordToggle"
                        >

                            <i
                                class="fa-regular fa-eye"
                                id="confirmPasswordEye"
                            ></i>

                        </button>

                    </div>

                    <span
                        class="error-message"
                        id="confirmPasswordError"
                    ></span>

                </div>


                <div class="input-group">

                    <label for="role">
                        Role
                    </label>

                    <div class="select-box">

                        <select
                            id="role"
                            name="role"
                        >

                            <option value="">
                                Landlord / Tenant
                            </option>

                            <option value="landlord">
                                Landlord
                            </option>

                            <option value="tenant">
                                Tenant
                            </option>

                        </select>

                        <i class="fa-solid fa-chevron-down"></i>

                    </div>

                    <span
                        class="error-message"
                        id="roleError"
                    ></span>

                </div>


                <div class="terms">

                    <label class="terms-label">

                        <input
                            type="checkbox"
                            id="terms"
                            name="terms"
                        >

                        <span class="custom-checkbox"></span>

                    </label>


                    <p>

                        I agree to the

                        <a href="#">
                            Terms and Conditions
                        </a>

                        and

                        <a href="#">
                            Privacy Policy.
                        </a>

                    </p>

                </div>

                <span
                    class="error-message terms-error"
                    id="termsError"
                ></span>


                <button
                    type="submit"
                    class="signup-button"
                >
                    Sign Up
                </button>


            </form>


            <div class="login-text">

                <span>
                    Already have an account?
                </span>

                <a href="login.php">
                    Log in
                </a>

            </div>


        </div>

    </div>


    <script src="../js/register.js"></script>

</body>

</html>