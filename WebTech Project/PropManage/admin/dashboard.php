<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'System Administrator';
$totalUsers = 5000;
$totalRevenue = 4200000;
$totalProps = 3245;
$recentActivity = [];

if ($db_connected && $pdo) {
    try {
        $uStmt = $pdo->query("SELECT COUNT(*) FROM users");
        $val = $uStmt->fetchColumn();
        if ($val > 0) $totalUsers = $val;

        $rStmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'completed'");
        $val2 = $rStmt->fetchColumn();
        if ($val2 > 0) $totalRevenue = $val2;

        $pStmt = $pdo->query("SELECT COUNT(*) FROM properties");
        $val3 = $pStmt->fetchColumn();
        if ($val3 > 0) $totalProps = $val3;

        $aStmt = $pdo->query("SELECT a.*, COALESCE(u.full_name, 'System') as user_name FROM audit_logs a LEFT JOIN users u ON a.user_id = u.user_id ORDER BY a.created_at DESC LIMIT 5");
        $recentActivity = $aStmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | PropManage</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>


<aside class="sidebar">
    <a href="../index.php" class="brand">
        <span class="brand-mark">◈</span>PropManage
    </a>
    <nav class="side-nav">
        <a class="side-link active" href="dashboard.php"><span class="side-icon">▦</span>Dashboard</a>
        <a class="side-link" href="users.php"><span class="side-icon">👥</span>User Management</a>
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
            <input type="text" placeholder="Search system...">
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
            <h1 class="page-title">Dashboard</h1>
        </div>


        <section class="stats">
            <div class="stat-card">
                <div class="stat-top">
                    Total Users
                    <span class="stat-icon" style="background:#eaf1fc; color:#0755cf;">👥</span>
                </div>
                <div class="stat-number"><?php echo number_format($totalUsers); ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    Total Revenue
                    <span class="stat-icon" style="background:#e7f8f1; color:#00a66f;">৳</span>
                </div>
                <div class="stat-number"><?php echo number_format($totalRevenue); ?> BDT</div>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    Total Properties
                    <span class="stat-icon" style="background:#fff4e5; color:#d67400;">🏠</span>
                </div>
                <div class="stat-number"><?php echo number_format($totalProps); ?></div>
            </div>
        </section>


        <section class="panel">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="panel-title" style="margin-bottom: 0;">Recent Activity</h2>
                <button type="button" class="outline-btn" style="padding: 4px 10px; font-size: 16px;" title="Activity Options">⋮</button>
            </div>

            <ul class="activity-feed">
                <?php if (!empty($recentActivity)): ?>
                    <?php foreach ($recentActivity as $act): ?>
                        <li class="activity-item">
                            <div class="activity-icon verified">✓</div>
                            <div class="activity-content">
                                <div class="activity-title"><?php echo htmlspecialchars($act['action_type']); ?>: <?php echo htmlspecialchars($act['details']); ?></div>
                                <div class="activity-meta"><?php echo date('M d, Y H:i', strtotime($act['created_at'])); ?> • User: <?php echo htmlspecialchars($act['user_name']); ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="activity-item">
                        <div class="activity-icon verified">✓</div>
                        <div class="activity-content">
                            <div class="activity-title">Account verified</div>
                            <div class="activity-meta">2 mins ago • Automated System</div>
                        </div>
                    </li>

                    <li class="activity-item">
                        <div class="activity-icon flagged">⚠</div>
                        <div class="activity-content">
                            <div class="activity-title">Listing #492 flagged for review</div>
                            <div class="activity-meta">15 mins ago • User Report</div>
                        </div>
                    </li>

                    <li class="activity-item">
                        <div class="activity-icon payout">💸</div>
                        <div class="activity-content">
                            <div class="activity-title">Payout processed for Gulshan Heritage</div>
                            <div class="activity-meta">1 hour ago • Finance Dept</div>
                        </div>
                    </li>

                    <li class="activity-item">
                        <div class="activity-icon agent">👤</div>
                        <div class="activity-content">
                            <div class="activity-title">New Agent Fariha Prity onboarded</div>
                            <div class="activity-meta">3 hours ago • HR Team</div>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>

            <div style="text-align: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f0edf0;">
                <a href="support.php" style="color: #0755cf; font-size: 13px; font-weight: 700; text-decoration: none;">
                    View All Activity →
                </a>
            </div>
        </section>


        <section class="panel">
            <h2 class="panel-title">System Operations</h2>
            <div class="quick-actions">
                <a href="users.php" class="quick-btn" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-decoration:none; color:inherit;">
                    <span class="quick-icon">👥</span>
                    Manage Users
                </a>
                <a href="properties.php" class="quick-btn" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-decoration:none; color:inherit;">
                    <span class="quick-icon">🔍</span>
                    Audit Listings
                </a>
                <a href="financials.php" class="quick-btn" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-decoration:none; color:inherit;">
                    <span class="quick-icon">💰</span>
                    Platform Payouts
                </a>
            </div>
        </section>
    </main>
</div>

<script src="../js/admin.js"></script>
<script>

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
