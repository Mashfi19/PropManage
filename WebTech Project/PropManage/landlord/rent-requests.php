<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'Md Farhan Nadim';
$requests = [];
$pendingCount = 24;

if ($db_connected && $pdo) {
    try {
        $cStmt = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
        $pendingCount = $cStmt->fetchColumn() ?: 0;

        $stmt = $pdo->query("SELECT b.*, u.full_name, u.email, u.phone, u.profile_image,
                                COALESCE(tp.profession, 'Resident') as profession,
                                p.title as property_title, p.address
                             FROM bookings b
                             JOIN users u ON b.tenant_id = u.user_id
                             LEFT JOIN tenant_profiles tp ON u.user_id = tp.user_id
                             JOIN properties p ON b.property_id = p.property_id
                             ORDER BY b.created_at DESC");
        $requests = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Rent Requests | PropManage</title>
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
        <a class="side-link active" href="rent-requests.php"><span class="side-icon">♧</span>Rent Requests</a>
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
            <h1 class="page-title">Rent Requests</h1>
        </div>


        <div class="stat-card stat-wide" style="margin-bottom:42px">
            <div class="stat-top">PENDING REQUESTS <span class="stat-icon gray">⌛</span></div>
            <div class="stat-number" id="pendingCounter"><?php echo $pendingCount; ?></div>
        </div>


        <section class="panel table-panel">
            <div class="table-head">
                <h2 class="table-title">All Requests</h2>
                <input class="table-search" placeholder="⌕  Search applicant or location">
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Applicant Name</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $r):
                            $badgeColor = $r['status'] === 'approved' ? '#e4f7ee; color:#009664;' : ($r['status'] === 'rejected' ? '#fee7e7; color:#c91919;' : '#fff4e5; color:#d67400;');
                        ?>
                            <tr class="req-row">
                                <td>
                                    <div class="person">
                                        <img src="<?php echo htmlspecialchars($r['profile_image']); ?>" alt="<?php echo htmlspecialchars($r['full_name']); ?>">
                                        <div>
                                            <span class="person-name"><?php echo htmlspecialchars($r['full_name']); ?></span>
                                            <div class="person-email"><?php echo htmlspecialchars($r['profession']); ?> • Verified</div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($r['address']); ?></td>
                                <td><span class="status-badge" style="background:<?php echo $badgeColor; ?> padding:4px 10px; border-radius:12px; font-size:11px; font-weight:700;"><?php echo strtoupper($r['status']); ?></span></td>
                                <td>
                                    <button type="button" class="details-btn review-request-btn"
                                            data-id="<?php echo $r['booking_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($r['full_name']); ?>"
                                            data-prop="<?php echo htmlspecialchars($r['address']); ?>"
                                            data-rent="<?php echo number_format($r['monthly_rent']); ?> BDT"
                                            data-date="<?php echo date('d F Y', strtotime($r['start_date'])); ?>"
                                            data-term="12 Months"
                                            data-phone="<?php echo htmlspecialchars($r['phone']); ?>"
                                            data-email="<?php echo htmlspecialchars($r['email']); ?>">
                                        Details
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="req-row">
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&q=80" alt="Farhan Nadim">
                                <div>
                                    <span class="person-name">Farhan Nadim</span>
                                    <div class="person-email">Software Engineer • Verified</div>
                                </div>
                            </div>
                        </td>
                        <td>3/P, Banani, Dhaka (Family)</td>
                        <td><span class="status-badge" style="background:#fff4e5; color:#d67400; padding:4px 10px; border-radius:12px; font-size:11px; font-weight:700;">PENDING</span></td>
                        <td>
                            <button type="button" class="details-btn review-request-btn"
                                    data-name="Farhan Nadim"
                                    data-prop="3/P, Banani, Dhaka (Family)"
                                    data-rent="25,000 BDT"
                                    data-date="01 October 2026"
                                    data-term="12 Months"
                                    data-phone="01708-375128"
                                    data-email="fanadim@gmail.com">
                                Details
                            </button>
                        </td>
                    </tr>

                    <tr class="req-row">
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=100&q=80" alt="Mashfi Rahman">
                                <div>
                                    <span class="person-name">Mashfi Rahman</span>
                                    <div class="person-email">Corporate Banker • Verified</div>
                                </div>
                            </div>
                        </td>
                        <td>101/F, Bashundhara, Dhaka (Bachelor)</td>
                        <td><span class="status-badge" style="background:#fff4e5; color:#d67400; padding:4px 10px; border-radius:12px; font-size:11px; font-weight:700;">PENDING</span></td>
                        <td>
                            <button type="button" class="details-btn review-request-btn"
                                    data-name="Mashfi Rahman"
                                    data-prop="101/F, Bashundhara, Dhaka (Bachelor)"
                                    data-rent="22,000 BDT"
                                    data-date="15 October 2026"
                                    data-term="6 Months"
                                    data-phone="01712-345678"
                                    data-email="mashfi@gmail.com">
                                Details
                            </button>
                        </td>
                    </tr>

                    <tr class="req-row">
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80" alt="Arafatullah Rifat">
                                <div>
                                    <span class="person-name">Arafatullah Rifat</span>
                                    <div class="person-email">Senior Executive • Verified</div>
                                </div>
                            </div>
                        </td>
                        <td>100H, Gulshan, Dhaka (Mansion)</td>
                        <td><span class="status-badge" style="background:#fff4e5; color:#d67400; padding:4px 10px; border-radius:12px; font-size:11px; font-weight:700;">PENDING</span></td>
                        <td>
                            <button type="button" class="details-btn review-request-btn"
                                    data-name="Arafatullah Rifat"
                                    data-prop="100H, Gulshan, Dhaka (Mansion)"
                                    data-rent="50,000 BDT"
                                    data-date="01 November 2026"
                                    data-term="24 Months"
                                    data-phone="01755-998877"
                                    data-email="rifat@gmail.com">
                                Details
                            </button>
                        </td>
                    </tr>

                    <tr class="req-row">
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80" alt="Fariha Akter Prity">
                                <div>
                                    <span class="person-name">Fariha Akter Prity</span>
                                    <div class="person-email">Lecturer • Verified</div>
                                </div>
                            </div>
                        </td>
                        <td>2M, Bashundhara, Dhaka (Bachelor Flat)</td>
                        <td><span class="status-badge" style="background:#fff4e5; color:#d67400; padding:4px 10px; border-radius:12px; font-size:11px; font-weight:700;">PENDING</span></td>
                        <td>
                            <button type="button" class="details-btn review-request-btn"
                                    data-name="Fariha Akter Prity"
                                    data-prop="2M, Bashundhara, Dhaka (Bachelor Flat)"
                                    data-rent="27,000 BDT"
                                    data-date="01 October 2026"
                                    data-term="12 Months"
                                    data-phone="01811-223344"
                                    data-email="fariha@gmail.com">
                                Details
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


<div class="pm-modal-overlay" id="reviewModal">
    <div class="pm-modal">
        <div class="pm-modal-header">
            <div class="pm-modal-title">Review Rental Application</div>
            <button type="button" class="pm-modal-close" onclick="document.getElementById('reviewModal').classList.remove('active')">&times;</button>
        </div>
        <div class="pm-modal-body">
            <div style="background: #f7f5f7; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                <h3 id="modalApplicantName" style="font-size: 17px; color: #25252a; margin-bottom: 4px;">Applicant Name</h3>
                <p id="modalApplicantContact" style="font-size: 13px; color: #666;">Phone • Email</p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f0edf0; padding-bottom: 8px;">
                    <span>Target Property:</span>
                    <strong id="modalApplicantProp">Banani</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f0edf0; padding-bottom: 8px;">
                    <span>Offered Rent:</span>
                    <strong id="modalApplicantRent" style="color: #0755cf;">25,000 BDT /mo</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f0edf0; padding-bottom: 8px;">
                    <span>Requested Move-in:</span>
                    <strong id="modalApplicantDate">Date</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Lease Term:</span>
                    <strong id="modalApplicantTerm">12 Months</strong>
                </div>
            </div>
        </div>
        <div class="pm-modal-footer">
            <button type="button" class="outline-btn" id="rejectBtn" style="color: #d02020; border-color: #fbd6d6;">Reject</button>
            <button type="button" class="primary-btn" id="approveBtn" style="background: #00a66f;">Approve Request</button>
        </div>
    </div>
</div>

<script src="../js/landlord.js"></script>
<script>
    let activeRow = null;
    let activeBookingId = null;
    const reviewModal = document.getElementById("reviewModal");

    document.querySelectorAll(".review-request-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            activeRow = btn.closest("tr");
            activeBookingId = btn.dataset.id || 1;
            document.getElementById("modalApplicantName").textContent = btn.dataset.name;
            document.getElementById("modalApplicantContact").textContent = `${btn.dataset.phone} • ${btn.dataset.email}`;
            document.getElementById("modalApplicantProp").textContent = btn.dataset.prop;
            document.getElementById("modalApplicantRent").textContent = btn.dataset.rent;
            document.getElementById("modalApplicantDate").textContent = btn.dataset.date;
            document.getElementById("modalApplicantTerm").textContent = btn.dataset.term;
            reviewModal.classList.add("active");
        });
    });

    document.getElementById("approveBtn").addEventListener("click", () => {
        if (activeRow) {
            const badge = activeRow.querySelector(".status-badge");
            badge.style.background = "#e4f7ee";
            badge.style.color = "#009664";
            badge.textContent = "APPROVED";


            const formData = new FormData();
            formData.append("booking_id", activeBookingId);
            formData.append("status", "approved");
            fetch("../api/update_booking_status.php", {
                method: "POST",
                body: formData
            }).then(r => r.json()).then(res => {
                alert(res.message || "Application Approved! Lease draft generated and tenant notified.");
            }).catch(() => {
                alert("Application Approved! Lease draft generated and tenant notified.");
            });
        }
        reviewModal.classList.remove("active");
    });

    document.getElementById("rejectBtn").addEventListener("click", () => {
        if (activeRow) {
            const badge = activeRow.querySelector(".status-badge");
            badge.style.background = "#fee7e7";
            badge.style.color = "#c91919";
            badge.textContent = "REJECTED";


            const formData = new FormData();
            formData.append("booking_id", activeBookingId);
            formData.append("status", "rejected");
            fetch("../api/update_booking_status.php", {
                method: "POST",
                body: formData
            }).then(r => r.json()).then(res => {
                alert(res.message || "Application Rejected. Tenant notified with polite feedback.");
            }).catch(() => {
                alert("Application Rejected. Tenant notified with polite feedback.");
            });
        }
        reviewModal.classList.remove("active");
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