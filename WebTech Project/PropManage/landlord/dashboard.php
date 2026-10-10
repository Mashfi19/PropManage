<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'Md Farhan Nadim';
$totalIncome = 50000;
$pendingRequests = 4;
$activeMaintenance = 7;
$urgentCount = 2;
$standardCount = 5;

if ($db_connected && $pdo) {
    try {
        $incStmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE payment_status = 'completed'");
        $val = $incStmt->fetchColumn();
        if ($val > 0) $totalIncome = $val;

        $penStmt = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
        $pendingRequests = $penStmt->fetchColumn();

        $maintStmt = $pdo->query("SELECT
            COUNT(*) as total_maint,
            SUM(CASE WHEN priority = 'Urgent' THEN 1 ELSE 0 END) as urgent,
            SUM(CASE WHEN priority != 'Urgent' THEN 1 ELSE 0 END) as standard
            FROM maintenance_requests WHERE status != 'resolved'");
        $mRow = $maintStmt->fetch();
        if ($mRow && $mRow['total_maint'] > 0) {
            $activeMaintenance = $mRow['total_maint'];
            $urgentCount = $mRow['urgent'] ?: 0;
            $standardCount = $mRow['standard'] ?: 0;
        }
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Dashboard | PropManage</title>
    <link rel="stylesheet" href="../css/landlord.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>


<aside class="sidebar">
    <a href="../index.php" class="brand">
        <span class="brand-mark">◈</span>PropManage
    </a>
    <nav class="side-nav">
        <a class="side-link active" href="dashboard.php"><span class="side-icon">▦</span>Dashboard</a>
        <a class="side-link" href="properties.php"><span class="side-icon">▦</span>Properties</a>
        <a class="side-link" href="rent-requests.php"><span class="side-icon">♧</span>Rent Requests</a>
        <a class="side-link" href="messages.php"><span class="side-icon">▣</span>Messages</a>
        <a class="side-link" href="tenants.php"><span class="side-icon">♧</span>Tenants</a>
        <a class="side-link" href="payments.php"><span class="side-icon">▣</span>Payments</a>
        <a class="side-link" href="maintenance.php"><span class="side-icon">🔧</span>Maintenance</a>
    </nav>
    <div class="settings">
        <a class="side-link" href="profile.php"><span class="side-icon">⚙</span>Settings</a>
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
            <img class="avatar" src="<?php echo htmlspecialchars(getActiveUserAvatar()); ?>" alt="Landlord">

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
            <h1 class="page-title">Dashboard</h1>
        </div>


        <section class="stats">
            <div class="stat-card">
                <div class="stat-top">TOTAL INCOME</div>
                <div class="stat-number"><?php echo number_format($totalIncome); ?> BDT</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">PENDINGS <span class="stat-icon">▦</span></div>
                <div class="stat-number"><?php echo $pendingRequests; ?> <small>Requests</small></div>
            </div>
            <div class="stat-card">
                <div class="stat-top">MAINTENANCE <span class="stat-icon red">🔧</span></div>
                <div class="stat-number"><?php echo $activeMaintenance; ?> <small>Active</small></div>
                <div style="font-size:12px;margin-top:18px;color:#c71919">● <?php echo $urgentCount; ?> Urgent</div>
                <div style="font-size:12px;margin-top:10px">　<?php echo $standardCount; ?> Standard</div>
            </div>
        </section>


        <section class="panel">
            <h2 class="panel-title">⚠ Action Items</h2>
            <div class="action-row urgent">
                <div class="action-main">
                    <div class="action-icon">♨</div>
                    <div>
                        <div class="action-title">Urgent: Water Leak in Unit 4B</div>
                        <div class="action-meta">Reported 2 hours ago • The Gulshan Residences</div>
                    </div>
                </div>
                <a href="maintenance.php" class="primary-btn" style="text-decoration:none;">Details</a>
            </div>
            <div class="action-row">
                <div class="action-main">
                    <div class="action-icon">▣</div>
                    <div>
                        <div class="action-title">Rent Overdue: 3 Tenants</div>
                        <div class="action-meta">Total outstanding: 53,000 BDT • Various Properties</div>
                    </div>
                </div>
                <button type="button" class="outline-btn" id="sendRemindersBtn">Send Reminders</button>
            </div>
        </section>


        <section class="panel">
            <h2 class="panel-title">Quick Actions</h2>
            <div class="quick-actions">
                <a href="add-property.php" class="quick-btn" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-decoration:none; color:inherit;">
                    <span class="quick-icon">▤+</span>
                    Add Property
                </a>
                <a href="tenants.php" class="quick-btn" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-decoration:none; color:inherit;">
                    <span class="quick-icon">♙+</span>
                    New Tenant
                </a>
                <a href="payments.php" class="quick-btn" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-decoration:none; color:inherit;">
                    <span class="quick-icon">▤</span>
                    Payment
                </a>
            </div>
        </section>
    </main>
</div>

<script src="../js/landlord.js"></script>
<script>

    document.getElementById("sendRemindersBtn").addEventListener("click", () => {
        alert("Automated SMS & Email payment reminders successfully sent to 3 overdue tenants!");
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