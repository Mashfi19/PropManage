<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'Mashfi Rahman';
$tenantId = $_SESSION['user_id'] ?? 3;
$myTickets = [];

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM maintenance_requests WHERE tenant_id = :tid OR :is_demo = 1 ORDER BY created_at DESC");
        $stmt->execute([':tid' => $tenantId, ':is_demo' => 1]);
        $myTickets = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance | PropManage</title>
    <link rel="stylesheet" href="../css/tenant.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>


<aside class="sidebar">
    <a href="../index.php" class="brand">
        <span class="brand-mark">◈</span>PropManage
    </a>
    <nav class="side-nav">
        <a class="side-link" href="explore.php"><span class="side-icon">▦</span>Explore</a>
        <a class="side-link" href="my-requests.php"><span class="side-icon">♧</span>My Requests</a>
        <a class="side-link" href="messages.php"><span class="side-icon">▣</span>Messages</a>
        <a class="side-link" href="payments.php"><span class="side-icon">▣</span>Payments</a>
        <a class="side-link active" href="maintenance.php"><span class="side-icon">🔧</span>Maintenance</a>
    </nav>
    <div class="settings">
        <a class="side-link" href="profile.php"><span class="side-icon">⚙</span>Settings</a>
    </div>
</aside>


<div class="main">

    <header class="topbar">
        <div class="global-search">
            <span>⌕</span>
            <input type="text" placeholder="Search maintenance...">
        </div>
        <div class="profile user-dropdown-container" id="profileDropdownBtn">
            <div class="profile-name">
                <?php echo htmlspecialchars($userName); ?>
                <div class="profile-role">Tenant</div>
            </div>
            <img class="avatar" src="<?php echo htmlspecialchars(getActiveUserAvatar()); ?>" alt="Tenant">

            <div class="user-dropdown-menu" id="userMenu">
                <div class="dropdown-header">Switch Role Preview</div>
                <a class="dropdown-item" href="../landlord/dashboard.php"><span>🏠</span> Landlord Portal</a>
                <a class="dropdown-item" href="../admin/dashboard.php"><span>🛡️</span> Admin Portal</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="profile.php"><span>👤</span> Edit Profile</a>
                <a class="dropdown-item" href="../auth/logout.php" style="color: #d02020;"><span>🚪</span> Log Out</a>
            </div>
        </div>
    </header>


    <main class="content">

        <section class="maintenance-form-panel">
            <h2>Submit New Request</h2>

            <form id="maintenanceForm">
                <label class="form-label" for="issueType">Issue Type</label>
                <input type="text" id="issueType" class="form-control-input" placeholder="[Topic Name] e.g. Plumbing, Electrical, Air Conditioning" required>

                <label class="form-label" for="issueDescription">Description</label>
                <textarea id="issueDescription" class="form-control-textarea" placeholder="Describe here..." required></textarea>

                <label class="form-label">Photo Attachment</label>
                <input type="file" id="maintenanceFile" accept="image/*" style="display: none;">

                <div class="photo-attachment-dropzone" id="photoDropzone">
                    <p>Click or drag files here to attach photos of the damage</p>
                    <div id="photoPreviewContainer" style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px;"></div>
                </div>

                <button type="submit" class="primary-btn" style="padding: 12px 28px;">
                    Submit Request
                </button>
            </form>
        </section>


        <section class="panel table-panel">
            <div class="table-head">
                <h2 class="table-title">My Maintenance Tickets</h2>
                <input class="table-search" placeholder="⌕  Search tickets">
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Category</th>
                        <th>Reported Date</th>
                        <th>Priority</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($myTickets)): ?>
                        <?php foreach ($myTickets as $tkt):
                            $stClass = $tkt['status'] === 'resolved' ? 'paid' : ($tkt['status'] === 'progress' ? 'pending' : 'pending');
                            $stLabel = $tkt['status'] === 'resolved' ? 'Resolved' : ($tkt['status'] === 'progress' ? 'In Progress' : 'Pending Review');
                            $priColor = $tkt['priority'] === 'Urgent' ? '#d02020; font-weight:700;' : '#426da8; font-weight:600;';
                        ?>
                            <tr>
                                <td><strong>#TKT-10<?php echo $tkt['request_id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($tkt['title']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($tkt['created_at'])); ?></td>
                                <td><span style="color: <?php echo $priColor; ?>"><?php echo htmlspecialchars($tkt['priority']); ?></span></td>
                                <td><span class="status-tag <?php echo $stClass; ?>"><?php echo $stLabel; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td><strong>#TKT-1092</strong></td>
                        <td>Kitchen Sink Drainage Leak</td>
                        <td>Today, 10:30 AM</td>
                        <td><span style="color: #d02020; font-weight: 700;">Urgent</span></td>
                        <td><span class="status-tag pending">Pending Review</span></td>
                    </tr>
                    <tr>
                        <td><strong>#TKT-1088</strong></td>
                        <td>Master Bedroom AC Servicing</td>
                        <td>Aug 27, 2026</td>
                        <td><span style="color: #426da8; font-weight: 600;">Standard</span></td>
                        <td><span class="status-tag pending" style="background:#eaf1fc; color:#0755cf;">In Progress</span></td>
                    </tr>
                    <tr>
                        <td><strong>#TKT-1045</strong></td>
                        <td>Balcony Door Latch Replacement</td>
                        <td>Aug 14, 2026</td>
                        <td><span style="color: #426da8; font-weight: 600;">Standard</span></td>
                        <td><span class="status-tag paid">Resolved</span></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

<script src="../js/tenant.js"></script>
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
