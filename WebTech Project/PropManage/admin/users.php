<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'System Administrator';
$usersList = [];
$totalUsers = 5000;

if ($db_connected && $pdo) {
    try {
        $cStmt = $pdo->query("SELECT COUNT(*) FROM users");
        $val = $cStmt->fetchColumn();
        if ($val > 0) $totalUsers = $val;

        $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        $usersList = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | PropManage</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>


<aside class="sidebar">
    <a href="../index.php" class="brand">
        <span class="brand-mark">◈</span>PropManage
    </a>
    <nav class="side-nav">
        <a class="side-link" href="dashboard.php"><span class="side-icon">▦</span>Dashboard</a>
        <a class="side-link active" href="users.php"><span class="side-icon">👥</span>User Management</a>
        <a class="side-link" href="properties.php"><span class="side-icon">🏢</span>Property Manage</a>
        <a class="side-link" href="support.php"><span class="side-icon">🛡️</span>Support Request</a>
        <a class="side-link" href="financials.php"><span class="side-icon">💰</span>Financial</a>
    </nav>
    <div class="settings">
        <a class="side-link" href="profile.php"><span class="side-icon">⚙</span>Settings</a>
    </div>
</aside>


<div class="main">

    <header class="topbar">
        <div class="global-search">
            <span>⌕</span>
            <input type="text" placeholder="Search users...">
        </div>
        <div class="profile user-dropdown-container" id="profileDropdownBtn">
            <div class="profile-name">
                <?php echo htmlspecialchars($userName); ?>
                <div class="profile-role">Admin</div>
            </div>
            <img class="avatar" src="<?php echo htmlspecialchars(getActiveUserAvatar()); ?>" alt="Admin">

            <div class="user-dropdown-menu" id="userMenu">
                <div class="dropdown-header">Switch Role Preview</div>
                <a class="dropdown-item" href="../landlord/dashboard.php"><span>🏠</span> Landlord Portal</a>
                <a class="dropdown-item" href="../tenant/explore.php"><span>🔑</span> Tenant Portal</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="profile.php"><span>👤</span> Edit Profile</a>
                <a class="dropdown-item" href="../auth/logout.php" style="color: #d02020;"><span>🚪</span> Log Out</a>
            </div>
        </div>
    </header>


    <main class="content">
        <div class="page-head">
            <h1 class="page-title">User Management</h1>
            <button type="button" class="primary-btn" id="openAddUserModal">
                ＋ Add User
            </button>
        </div>

        <div class="stat-card stat-wide" style="margin-bottom: 36px;">
            <div class="stat-top">
                TOTAL USERS
                <span class="stat-icon gray">👥</span>
            </div>
            <div class="stat-number"><?php echo number_format($totalUsers); ?></div>
        </div>


        <div class="filters">
            <div class="segment">
                <button type="button" class="active" onclick="filterUserRole('all')">All</button>
                <button type="button" onclick="filterUserRole('tenant')">Tenants</button>
                <button type="button" onclick="filterUserRole('landlord')">Landlords</button>
            </div>
            <select class="filter-select" onchange="filterUserStatus(this.value)">
                <option value="">All Statuses</option>
                <option>Active</option>
                <option>Pending Verification</option>
                <option>Suspended</option>
            </select>
        </div>


        <section class="panel table-panel">
            <table class="table">
                <thead>
                    <tr>
                        <th>USER DETAILS</th>
                        <th>ROLE</th>
                        <th>STATUS</th>
                        <th>REGISTRATION</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usersList)): ?>
                        <?php foreach ($usersList as $u):
                            $statusDot = $u['status'] === 'active' ? 'active' : 'pending';
                            $statusText = $u['status'] === 'active' ? 'Active' : ucfirst($u['status']);
                        ?>
                            <tr class="user-table-row" data-role="<?php echo $u['role']; ?>">
                                <td>
                                    <div class="person">
                                        <img src="<?php echo htmlspecialchars($u['profile_image']); ?>" alt="<?php echo htmlspecialchars($u['full_name']); ?>">
                                        <div>
                                            <div class="person-name"><?php echo htmlspecialchars($u['full_name']); ?></div>
                                            <div class="person-email"><?php echo htmlspecialchars($u['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="role-badge <?php echo $u['role']; ?>"><?php echo ucfirst($u['role']); ?></span></td>
                                <td><span class="user-status-dot <?php echo $statusDot; ?>"><?php echo $statusText; ?></span></td>
                                <td><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                                <td>
                                    <?php if ($u['status'] === 'active'): ?>
                                        <button type="button" class="outline-btn user-action-btn" data-action="suspend" style="padding: 6px 12px; font-size: 12px; color: #d02020;" onclick="alert('User <?php echo htmlspecialchars($u['full_name']); ?> suspended.')">
                                            Suspend
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="primary-btn user-action-btn" data-action="verify" style="padding: 6px 12px; font-size: 12px; background: #00a66f;" onclick="alert('User <?php echo htmlspecialchars($u['full_name']); ?> verified successfully!')">
                                            Verify
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr class="user-table-row" data-role="landlord">
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&q=80" alt="Farhan Nadim">
                                <div>
                                    <div class="person-name">Md Farhan Nadim</div>
                                    <div class="person-email">fanadim@gmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="role-badge landlord">Landlord</span></td>
                        <td><span class="user-status-dot active">Active</span></td>
                        <td>12/03/2026</td>
                        <td>
                            <button type="button" class="outline-btn user-action-btn" data-action="suspend" style="padding: 6px 12px; font-size: 12px; color: #d02020;">
                                Suspend
                            </button>
                        </td>
                    </tr>

                    <tr class="user-table-row" data-role="tenant">
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=100&q=80" alt="Mashfi Rahman">
                                <div>
                                    <div class="person-name">Mashfi Rahman</div>
                                    <div class="person-email">mashfi@gmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="role-badge tenant">Tenant</span></td>
                        <td><span class="user-status-dot pending">Pending Verification</span></td>
                        <td>Today</td>
                        <td>
                            <button type="button" class="primary-btn user-action-btn" data-action="verify" style="padding: 6px 12px; font-size: 12px; background: #00a66f;">
                                Verify
                            </button>
                        </td>
                    </tr>

                    <tr class="user-table-row" data-role="landlord">
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80" alt="Arafatullah Rifat">
                                <div>
                                    <div class="person-name">Arafatullah Rifat</div>
                                    <div class="person-email">rifat@gmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="role-badge landlord">Landlord</span></td>
                        <td><span class="user-status-dot suspended">Suspended</span></td>
                        <td>18/01/2026</td>
                        <td>
                            <button type="button" class="outline-btn user-action-btn" data-action="activate" style="padding: 6px 12px; font-size: 12px; color: #00a66f;">
                                Activate
                            </button>
                        </td>
                    </tr>

                    <tr class="user-table-row" data-role="tenant">
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80" alt="Fariha Akter Prity">
                                <div>
                                    <div class="person-name">Fariha Akter Prity</div>
                                    <div class="person-email">fariha@gmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="role-badge tenant">Tenant</span></td>
                        <td><span class="user-status-dot active">Active</span></td>
                        <td>05/02/2026</td>
                        <td>
                            <button type="button" class="outline-btn user-action-btn" data-action="suspend" style="padding: 6px 12px; font-size: 12px; color: #d02020;">
                                Suspend
                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="table-footer">
                <span>Showing 1 to 4 of 5000 users</span>
                <span>‹ Previous　 <b>1</b>　2　3　…　124　 Next ›</span>
            </div>
        </section>
    </main>
</div>


<div class="pm-modal-overlay" id="addUserModal">
    <div class="pm-modal">
        <div class="pm-modal-header">
            <div class="pm-modal-title">Add New User to System</div>
            <button type="button" class="pm-modal-close" onclick="document.getElementById('addUserModal').classList.remove('active')">&times;</button>
        </div>
        <form id="addUserForm" onsubmit="event.preventDefault(); alert('User created successfully and verification email sent!'); document.getElementById('addUserModal').classList.remove('active');">
            <div class="pm-modal-body">
                <div class="form-group-pm">
                    <label>Full Name</label>
                    <input type="text" placeholder="e.g. Tanvir Ahmed" required>
                </div>
                <div class="form-group-pm">
                    <label>Email Address</label>
                    <input type="email" placeholder="e.g. tanvir@example.com" required>
                </div>
                <div class="form-group-pm">
                    <label>Role</label>
                    <select required>
                        <option value="tenant">Tenant</option>
                        <option value="landlord">Landlord</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div class="form-group-pm">
                    <label>Initial Password</label>
                    <input type="password" placeholder="Temporary password" required>
                </div>
            </div>
            <div class="pm-modal-footer">
                <button type="button" class="outline-btn" onclick="document.getElementById('addUserModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="primary-btn">Create User</button>
            </div>
        </form>
    </div>
</div>

<script src="../js/admin.js"></script>
<script>

    const addBtn = document.getElementById("openAddUserModal");
    const addModal = document.getElementById("addUserModal");
    if (addBtn && addModal) {
        addBtn.addEventListener("click", () => addModal.classList.add("active"));
    }


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
