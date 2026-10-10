<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'Mashfi Rahman';
$tenantId = $_SESSION['user_id'] ?? 3;
$myRequests = [];

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT b.*, p.title as property_title, p.address,
                                      u.full_name as landlord_name, u.phone as landlord_phone, u.profile_image as landlord_avatar
                               FROM bookings b
                               JOIN properties p ON b.property_id = p.property_id
                               JOIN users u ON p.landlord_id = u.user_id
                               WHERE b.tenant_id = :tid OR :is_demo = 1
                               ORDER BY b.created_at DESC");
        $stmt->execute([':tid' => $tenantId, ':is_demo' => 1]);
        $myRequests = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests | PropManage</title>
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
        <a class="side-link active" href="my-requests.php"><span class="side-icon">♧</span>My Requests</a>
        <a class="side-link" href="messages.php"><span class="side-icon">▣</span>Messages</a>
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
            <input type="text" placeholder="Search requests...">
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
        <div class="page-head">
            <h1 class="page-title">My Requests</h1>
        </div>

        <section class="panel table-panel">
            <div class="table-head">
                <h2 class="table-title">Rental Applications</h2>
                <input class="table-search" placeholder="⌕  Search landlord or location">
            </div>


            <table class="table">
                <thead>
                    <tr>
                        <th>Landlord Name</th>
                        <th>Location</th>
                        <th>Monthly Rent</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($myRequests)): ?>
                        <?php foreach ($myRequests as $mr):
                            $statusClass = $mr['status'] === 'approved' ? 'approved' : ($mr['status'] === 'rejected' ? 'rejected' : 'pending');
                        ?>
                            <tr>
                                <td>
                                    <div class="person">
                                        <img src="<?php echo htmlspecialchars($mr['landlord_avatar']); ?>" alt="<?php echo htmlspecialchars($mr['landlord_name']); ?>">
                                        <div>
                                            <span class="person-name"><?php echo htmlspecialchars($mr['landlord_name']); ?></span>
                                            <div class="person-email">Verified Owner</div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($mr['address']); ?></td>
                                <td><b><?php echo number_format($mr['monthly_rent']); ?> BDT</b></td>
                                <td><span class="status-tag <?php echo $statusClass; ?>"><?php echo strtoupper($mr['status']); ?></span></td>
                                <td>
                                    <button type="button" class="details-btn view-request-btn"
                                            data-landlord="<?php echo htmlspecialchars($mr['landlord_name']); ?>"
                                            data-property="<?php echo htmlspecialchars($mr['property_title']); ?>"
                                            data-rent="<?php echo number_format($mr['monthly_rent']); ?> BDT"
                                            data-status="<?php echo ucfirst($mr['status']); ?>"
                                            data-date="Applied on <?php echo date('M d, Y', strtotime($mr['created_at'])); ?>">
                                        View
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&q=80" alt="Farhan Nadim">
                                <div>
                                    <span class="person-name">Farhan Nadim</span>
                                    <div class="person-email">Verified Owner</div>
                                </div>
                            </div>
                        </td>
                        <td>3/P, Banani, Dhaka (Family)</td>
                        <td><b>25,000 BDT</b></td>
                        <td><span class="status-tag pending">PENDING REVIEW</span></td>
                        <td>
                            <button type="button" class="details-btn view-request-btn"
                                    data-landlord="Farhan Nadim"
                                    data-property="Family Flat (3/P Banani)"
                                    data-rent="25,000 BDT"
                                    data-status="Pending Review"
                                    data-date="Applied on Aug 26, 2026">
                                View
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=100&q=80" alt="Mashfi Rahman">
                                <div>
                                    <span class="person-name">Mashfi Rahman</span>
                                    <div class="person-email">Verified Owner</div>
                                </div>
                            </div>
                        </td>
                        <td>101/F, Bashundhara, Dhaka (Bachelor)</td>
                        <td><b>22,000 BDT</b></td>
                        <td><span class="status-tag approved">APPROVED</span></td>
                        <td>
                            <button type="button" class="details-btn view-request-btn"
                                    data-landlord="Mashfi Rahman"
                                    data-property="Bachelor Unit (101/F Bashundhara)"
                                    data-rent="22,000 BDT"
                                    data-status="Approved"
                                    data-date="Applied on Aug 15, 2026">
                                View
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80" alt="Arafatullah Rifat">
                                <div>
                                    <span class="person-name">Arafatullah Rifat</span>
                                    <div class="person-email">Verified Owner</div>
                                </div>
                            </div>
                        </td>
                        <td>1204 Gulshan, Dhaka (Bachelor)</td>
                        <td><b>19,000 BDT</b></td>
                        <td><span class="status-tag pending">PENDING REVIEW</span></td>
                        <td>
                            <button type="button" class="details-btn view-request-btn"
                                    data-landlord="Arafatullah Rifat"
                                    data-property="Bachelor Flat (1204 Gulshan)"
                                    data-rent="19,000 BDT"
                                    data-status="Pending Review"
                                    data-date="Applied on Aug 24, 2026">
                                View
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80" alt="Fariha Akter Prity">
                                <div>
                                    <span class="person-name">Fariha Akter Prity</span>
                                    <div class="person-email">Verified Owner</div>
                                </div>
                            </div>
                        </td>
                        <td>100H, Gulshan, Dhaka (Mansion)</td>
                        <td><b>50,000 BDT</b></td>
                        <td><span class="status-tag rejected">REJECTED</span></td>
                        <td>
                            <button type="button" class="details-btn view-request-btn"
                                    data-landlord="Fariha Akter Prity"
                                    data-property="Mansion (100H Gulshan)"
                                    data-rent="50,000 BDT"
                                    data-status="Rejected (Unit Already Booked)"
                                    data-date="Applied on Aug 10, 2026">
                                View
                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="table-footer">
                <span>Showing 1-4 of 24</span>
                <span>‹　›</span>
            </div>
        </section>
    </main>
</div>


<div class="pm-modal-overlay" id="requestDetailModal">
    <div class="pm-modal">
        <div class="pm-modal-header">
            <div class="pm-modal-title">Application Details</div>
            <button type="button" class="pm-modal-close" onclick="document.getElementById('requestDetailModal').classList.remove('active')">&times;</button>
        </div>
        <div class="pm-modal-body">
            <div style="margin-bottom: 18px;">
                <label style="font-size: 11px; text-transform: uppercase; color: #888; font-weight: 700;">Landlord</label>
                <div id="modalLandlord" style="font-size: 16px; font-weight: 700; color: #25252a; margin-top: 4px;"></div>
            </div>
            <div style="margin-bottom: 18px;">
                <label style="font-size: 11px; text-transform: uppercase; color: #888; font-weight: 700;">Property</label>
                <div id="modalProperty" style="font-size: 15px; color: #444; margin-top: 4px;"></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div>
                    <label style="font-size: 11px; text-transform: uppercase; color: #888; font-weight: 700;">Monthly Rent</label>
                    <div id="modalRent" style="font-size: 15px; font-weight: 700; color: #0755cf; margin-top: 4px;"></div>
                </div>
                <div>
                    <label style="font-size: 11px; text-transform: uppercase; color: #888; font-weight: 700;">Status</label>
                    <div id="modalStatus" style="font-size: 14px; font-weight: 700; margin-top: 4px;"></div>
                </div>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="font-size: 11px; text-transform: uppercase; color: #888; font-weight: 700;">Submission Timeline</label>
                <div id="modalDate" style="font-size: 13px; color: #666; margin-top: 4px;"></div>
            </div>
        </div>
        <div class="pm-modal-footer">
            <a href="messages.php" class="outline-btn" style="text-decoration:none;">Message Landlord</a>
            <button type="button" class="primary-btn" onclick="document.getElementById('requestDetailModal').classList.remove('active')">Close</button>
        </div>
    </div>
</div>

<script src="../js/tenant.js"></script>
<script>

    const modal = document.getElementById("requestDetailModal");
    document.querySelectorAll(".view-request-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("modalLandlord").textContent = btn.dataset.landlord;
            document.getElementById("modalProperty").textContent = btn.dataset.property;
            document.getElementById("modalRent").textContent = btn.dataset.rent;
            document.getElementById("modalStatus").textContent = btn.dataset.status;
            document.getElementById("modalDate").textContent = btn.dataset.date;
            modal.classList.add("active");
        });
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
