<?php
require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user_id'] ?? 2;
$userName = $_SESSION['user_name'] ?? 'Md Farhan Nadim';
$userEmail = $_SESSION['user_email'] ?? 'farhanndm23@gmail.com';
$userPhone = '01708-375128';
$userImage = 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=160&q=80';
$successMsg = '';

if ($db_connected && $pdo) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');


        $uploadedAvatar = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['avatar']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($ext, $allowed)) {
                $dir = __DIR__ . '/../uploads/avatars/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $newName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($tmp, $dir . $newName)) {
                    $uploadedAvatar = '../uploads/avatars/' . $newName;
                }
            }
        }

        if ($name && $email) {
            if ($uploadedAvatar) {
                $uStmt = $pdo->prepare("UPDATE users SET full_name = :name, email = :email, phone = :phone, profile_image = :avatar WHERE user_id = :uid");
                $uStmt->execute([':name' => $name, ':email' => $email, ':phone' => $phone, ':avatar' => $uploadedAvatar, ':uid' => $userId]);
                $_SESSION['avatar'] = $uploadedAvatar;
                $userImage = $uploadedAvatar;
            } else {
                $uStmt = $pdo->prepare("UPDATE users SET full_name = :name, email = :email, phone = :phone WHERE user_id = :uid");
                $uStmt->execute([':name' => $name, ':email' => $email, ':phone' => $phone, ':uid' => $userId]);
            }
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $userName = $name;
            $userEmail = $email;
            $userPhone = $phone;
            $successMsg = 'Profile updated successfully!';
        }
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :uid");
        $stmt->execute([':uid' => $userId]);
        $u = $stmt->fetch();
        if ($u) {
            $userName = $u['full_name'];
            $userEmail = $u['email'];
            $userPhone = $u['phone'] ?? $userPhone;
            $userImage = !empty($u['profile_image']) ? $u['profile_image'] : $userImage;
            $_SESSION['avatar'] = $userImage;
        }
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | PropManage</title>
    <link rel="stylesheet" href="../css/landlord.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>


<aside class="sidebar">
    <a href="../index.php" class="brand">
        <span class="brand-mark">◈</span>PropManage
    </a>
    <nav class="side-nav">
        <a class="side-link" href="dashboard.php"><span class="side-icon">▦</span>Dashboard</a>
        <a class="side-link" href="properties.php"><span class="side-icon">▦</span>Properties</a>
        <a class="side-link" href="rent-requests.php"><span class="side-icon">♧</span>Rent Requests</a>
        <a class="side-link" href="messages.php"><span class="side-icon">▣</span>Messages</a>
        <a class="side-link" href="tenants.php"><span class="side-icon">♧</span>Tenants</a>
        <a class="side-link" href="payments.php"><span class="side-icon">▣</span>Payments</a>
        <a class="side-link" href="maintenance.php"><span class="side-icon">🔧</span>Maintenance</a>
    </nav>
    <div class="settings">
        <a class="side-link active" href="profile.php"><span class="side-icon">⚙</span>Settings</a>
    </div>
</aside>


<div class="main">

    <header class="topbar">
        <div class="global-search">
            <span>⌕</span>
            <input type="text" placeholder="Search...">
        </div>
        <div class="profile user-dropdown-container" id="profileDropdownBtn">
            <div class="profile-name">
                <?php echo htmlspecialchars($userName); ?>
                <div class="profile-role">Landlord</div>
            </div>
            <img class="avatar" src="<?php echo htmlspecialchars($userImage); ?>" alt="Landlord">

            <div class="user-dropdown-menu" id="userMenu">
                <div class="dropdown-header">Switch Role Preview</div>
                <a class="dropdown-item" href="../tenant/explore.php"><span>🔑</span> Tenant Portal</a>
                <a class="dropdown-item" href="../admin/dashboard.php"><span>🛡️</span> Admin Portal</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="profile.php"><span>👤</span> Edit Profile</a>
                <a class="dropdown-item" href="../auth/logout.php" style="color: #d02020;"><span>🚪</span> Log Out</a>
            </div>
        </div>
    </header>


    <main class="content">
        <div class="page-head">
            <h1 class="page-title">Edit Profile</h1>
        </div>

        <?php if ($successMsg): ?>
            <div style="background:#e4f7ee; color:#009664; padding:12px 18px; border-radius:8px; margin-bottom:20px; font-weight:600;">
                ✓ <?php echo htmlspecialchars($successMsg); ?>
            </div>
        <?php endif; ?>

        <div class="profile-grid">

            <section class="profile-card">
                <div class="profile-card-header">
                    <span class="icon">👤</span>
                    <h2>Personal Information</h2>
                </div>

                <form id="personalInfoForm" method="POST" action="profile.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="profile-pic-section">
                        <img id="avatarPreview" class="profile-pic-preview" src="<?php echo htmlspecialchars($userImage); ?>" alt="Landlord Avatar">
                        <div>
                            <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 8px;">Profile Picture</label>
                            <div class="profile-pic-actions">
                                <input type="file" name="avatar" id="avatarFileInput" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
                                <button type="button" class="outline-btn" style="padding: 7px 14px; font-size: 12px;" onclick="document.getElementById('avatarFileInput').click()">
                                    Upload New
                                </button>
                                <button type="button" class="outline-btn" style="padding: 7px 14px; font-size: 12px; color: #d02020;" onclick="removeAvatar()">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group-pm">
                            <label for="profName">Full Name</label>
                            <input type="text" name="name" id="profName" value="<?php echo htmlspecialchars($userName); ?>" required>
                        </div>
                        <div class="form-group-pm">
                            <label for="profRole">Role</label>
                            <input type="text" id="profRole" value="Owner / Landlord" disabled style="background: #f7f5f7; color: #777;">
                        </div>
                    </div>

                    <div class="form-group-pm">
                        <label for="profEmail">Email Address</label>
                        <input type="email" name="email" id="profEmail" value="<?php echo htmlspecialchars($userEmail); ?>" required>
                    </div>

                    <div class="form-group-pm">
                        <label for="profPhone">Phone Number</label>
                        <input type="tel" name="phone" id="profPhone" value="<?php echo htmlspecialchars($userPhone); ?>" required>
                    </div>

                    <div class="btn-row">
                        <button type="submit" class="primary-btn">Save Changes</button>
                        <button type="reset" class="outline-btn">Cancel</button>
                    </div>
                </form>
            </section>


            <section class="profile-card">
                <div class="profile-card-header">
                    <span class="icon">🛡️</span>
                    <h2>Security Settings</h2>
                </div>

                <form id="securityForm">
                    <div class="form-group-pm">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" placeholder="Enter current password" required>
                    </div>

                    <div class="form-group-pm">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" placeholder="Enter new password" required>
                    </div>

                    <div class="form-group-pm">
                        <label for="confirmNewPassword">Confirm New Password</label>
                        <input type="password" id="confirmNewPassword" placeholder="Confirm new password" required>
                    </div>

                    <div class="btn-row">
                        <button type="submit" class="primary-btn" style="width: 100%;">
                            Update Password
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>

<script>

    const fileInput = document.getElementById("avatarFileInput");
    const avatarPreview = document.getElementById("avatarPreview");
    if (fileInput) {
        fileInput.addEventListener("change", () => {
            if (fileInput.files.length > 0) {
                const reader = new FileReader();
                reader.onload = (e) => avatarPreview.src = e.target.result;
                reader.readAsDataURL(fileInput.files[0]);
            }
        });
    }

    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeAvatar() {
        document.getElementById('avatarFileInput').value = '';
        document.getElementById('avatarPreview').src = '../images/default-avatar.svg';
    }

    document.getElementById("securityForm").addEventListener("submit", (e) => {
        e.preventDefault();
        const p1 = document.getElementById("newPassword").value;
        const p2 = document.getElementById("confirmNewPassword").value;
        if (p1 !== p2) {
            alert("New passwords do not match. Please verify.");
            return;
        }
        alert("Password updated successfully!");
        document.getElementById("securityForm").reset();
    });


    const profileBtn = document.getElementById("profileDropdownBtn");
    const userMenu = document.getElementById("userMenu");
    if (profileBtn && userMenu) {
        profileBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            userMenu.classList.toggle("show");
        });
        document.addEventListener("click", () => userMenu.classList.remove("show"));
    }
</script>
</body>
</html>
