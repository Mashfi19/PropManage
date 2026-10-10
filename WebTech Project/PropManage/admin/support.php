<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'System Administrator';
$auditLogs = [];
$totalEvents = 148;

if ($db_connected && $pdo) {
    try {
        $cStmt = $pdo->query("SELECT COUNT(*) FROM audit_logs");
        $val = $cStmt->fetchColumn();
        if ($val > 0) $totalEvents = $val;

        $stmt = $pdo->query("SELECT a.*, COALESCE(u.full_name, 'System') as user_name
                             FROM audit_logs a
                             LEFT JOIN users u ON a.user_id = u.user_id
                             ORDER BY a.created_at DESC");
        $auditLogs = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support & Audit Logs | PropManage</title>
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
        <a class="side-link" href="users.php"><span class="side-icon">👥</span>User Management</a>
        <a class="side-link" href="properties.php"><span class="side-icon">🏢</span>Property Manage</a>
        <a class="side-link active" href="support.php"><span class="side-icon">🛡️</span>Support Request</a>
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
            <input type="text" placeholder="Search system audit logs...">
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
            <h1 class="page-title">Support &amp; Audit Logs</h1>
        </div>


        <section class="stats" style="margin-bottom: 32px;">
            <div class="stat-card">
                <div class="stat-top">OPEN INQUIRIES <span class="stat-icon gray">✉</span></div>
                <div class="stat-number">12 <small>Active</small></div>
            </div>
            <div class="stat-card">
                <div class="stat-top">AUDIT EVENTS TODAY <span class="stat-icon" style="background:#e7f8f1; color:#00a66f;">📋</span></div>
                <div class="stat-number"><?php echo number_format($totalEvents); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-top">SYSTEM HEALTH <span class="stat-icon" style="background:#eaf1fc; color:#0755cf;">⚡</span></div>
                <div class="stat-number" style="color: #00a66f;">99.98%</div>
            </div>
        </section>


        <section class="panel table-panel">
            <div class="table-head">
                <h2 class="table-title">System Audit Log</h2>
                <input class="table-search" placeholder="⌕  Filter audit events">
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Action Type</th>
                        <th>User / Actor</th>
                        <th>IP Address</th>
                        <th>Details</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($auditLogs)): ?>
                        <?php foreach ($auditLogs as $al): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($al['created_at'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($al['action_type']); ?></strong></td>
                                <td><?php echo htmlspecialchars($al['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($al['ip_address'] ?? '127.0.0.1'); ?></td>
                                <td><?php echo htmlspecialchars($al['details']); ?></td>
                                <td><span class="status-tag paid">SUCCESS</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td>02/09/2026 15:42</td>
                        <td><strong>User Verification</strong></td>
                        <td>Admin Nadim</td>
                        <td>103.205.71.12</td>
                        <td>Approved Landlord NID & Property Deeds for Unit 7B</td>
                        <td><span class="status-tag paid">SUCCESS</span></td>
                    </tr>
                    <tr>
                        <td>02/09/2026 14:10</td>
                        <td><strong>Payout Disbursed</strong></td>
                        <td>Finance Admin</td>
                        <td>103.205.71.14</td>
                        <td>Transfer of 24,500 BDT to Landlord Rifat Bank Account</td>
                        <td><span class="status-tag paid">SUCCESS</span></td>
                    </tr>
                    <tr>
                        <td>02/09/2026 12:05</td>
                        <td><strong>Listing Flagged</strong></td>
                        <td>User Report Bot</td>
                        <td>182.160.118.9</td>
                        <td>Listing #492 reported for duplicate images</td>
                        <td><span class="status-tag pending">UNDER REVIEW</span></td>
                    </tr>
                    <tr>
                        <td>02/09/2026 09:30</td>
                        <td><strong>Password Reset</strong></td>
                        <td>Tenant Mashfi</td>
                        <td>118.179.44.20</td>
                        <td>Security token authenticated via email</td>
                        <td><span class="status-tag paid">SUCCESS</span></td>
                    </tr>
                    <tr>
                        <td>01/09/2026 23:18</td>
                        <td><strong>Failed Login Attempt</strong></td>
                        <td>Unknown (guest)</td>
                        <td>45.112.55.80</td>
                        <td>3 incorrect password attempts for user #204</td>
                        <td><span class="status-tag rejected">BLOCKED</span></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="table-footer">
                <span>Showing 1-5 of 148 audit logs</span>
                <span>‹　<b>1</b>　2　3　›</span>
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
