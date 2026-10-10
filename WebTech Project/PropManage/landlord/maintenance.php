<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'Md Farhan Nadim';
$maintList = [];

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->query("SELECT m.*, u.full_name as tenant_name, u.profile_image,
                                COALESCE(p.title, 'Family Flat') as property_title,
                                COALESCE(p.address, 'Banani') as address
                             FROM maintenance_requests m
                             JOIN users u ON m.tenant_id = u.user_id
                             LEFT JOIN properties p ON m.property_id = p.property_id
                             ORDER BY m.created_at DESC");
        $maintList = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Maintenance | PropManage</title>
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
            <h1 class="page-title">Maintenance</h1>
        </div>


        <div class="filters maintenance-tabs">
            <div class="segment">
                <button type="button" class="active" onclick="filterMaint('all')">All</button>
                <button type="button" onclick="filterMaint('pending')">Pending</button>
                <button type="button" onclick="filterMaint('progress')">In Progress</button>
                <button type="button" onclick="filterMaint('resolved')">Resolved</button>
            </div>
        </div>


        <div class="maintenance-grid">
            <?php if (!empty($maintList)): ?>
                <?php foreach ($maintList as $m):
                    $badgeClass = $m['status'] === 'resolved' ? 'resolved' : ($m['status'] === 'progress' ? 'progress' : 'pending');
                    $badgeIcon = $m['status'] === 'resolved' ? '✓ Resolved' : ($m['status'] === 'progress' ? '🔧 In Progress' : '◉ Pending');
                ?>
                    <div class="maintenance-card" data-status="<?php echo $m['status']; ?>">
                        <div class="maintenance-main">
                            <div class="maintenance-status">
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $badgeIcon; ?></span>
                                <span><?php echo date('d M, Y', strtotime($m['created_at'])); ?></span>
                            </div>
                            <h3><?php echo htmlspecialchars($m['title']); ?></h3>
                            <p class="maintenance-msg"><?php echo htmlspecialchars($m['description']); ?></p>
                            <div class="maintenance-person">
                                <img src="<?php echo htmlspecialchars($m['profile_image']); ?>" alt="<?php echo htmlspecialchars($m['tenant_name']); ?>">
                                <div>
                                    <strong><?php echo htmlspecialchars($m['tenant_name']); ?></strong>
                                    <p><?php echo htmlspecialchars($m['property_title']); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="maintenance-actions">
                            <button type="button" class="update-btn">Update Status ⌄</button>
                            <a class="view-link maint-view-link" href="#"
                               data-title="<?php echo htmlspecialchars($m['title']); ?>"
                               data-tenant="<?php echo htmlspecialchars($m['tenant_name']); ?>"
                               data-unit="<?php echo htmlspecialchars($m['property_title']); ?>"
                               data-msg="<?php echo htmlspecialchars($m['description']); ?>"
                               data-priority="<?php echo htmlspecialchars($m['priority']); ?>">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>

            <div class="maintenance-card" data-status="pending">
                <div class="maintenance-main">
                    <div class="maintenance-status">
                        <span class="badge pending">◉ Pending</span>
                        <span>2h ago</span>
                    </div>
                    <h3>Water Leakage</h3>
                    <p class="maintenance-msg">Severe leakage under the kitchen sink. Water accumulating fast.</p>
                    <div class="maintenance-person">
                        <img src="https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=100&q=80" alt="Tenant">
                        <div>
                            <strong>Mashfi Rahman</strong>
                            <p>Family Flat • Unit 7B</p>
                        </div>
                    </div>
                </div>
                <div class="maintenance-actions">
                    <button type="button" class="update-btn">Update Status ⌄</button>
                    <a class="view-link maint-view-link" href="#" data-title="Water Leakage" data-tenant="Mashfi Rahman" data-unit="Family Flat • Unit 7B" data-msg="Severe leakage under the kitchen sink. Water accumulating fast." data-priority="Urgent">View Details</a>
                </div>
            </div>


            <div class="maintenance-card" data-status="progress">
                <div class="maintenance-main">
                    <div class="maintenance-status">
                        <span class="badge progress">🔧 In Progress</span>
                        <span>Yesterday</span>
                    </div>
                    <h3>Electrical Trip</h3>
                    <p class="maintenance-msg">Circuit breaker tripping frequently when operating the water heater.</p>
                    <div class="maintenance-person">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80" alt="Tenant">
                        <div>
                            <strong>Fariha Akter</strong>
                            <p>Bachelor • Unit 9C</p>
                        </div>
                    </div>
                </div>
                <div class="maintenance-actions">
                    <button type="button" class="update-btn">Update Status ⌄</button>
                    <a class="view-link maint-view-link" href="#" data-title="Electrical Trip" data-tenant="Fariha Akter" data-unit="Bachelor • Unit 9C" data-msg="Circuit breaker tripping frequently when operating the water heater." data-priority="Standard">View Details</a>
                </div>
            </div>


            <div class="maintenance-card" data-status="resolved">
                <div class="maintenance-main">
                    <div class="maintenance-status">
                        <span class="badge resolved">✓ Resolved</span>
                        <span>Oct 12, 2025</span>
                    </div>
                    <h3>Door Lock Replacement</h3>
                    <p class="maintenance-msg">Front entrance digital lock malfunctioned. Serviced by technician.</p>
                    <div class="maintenance-person">
                        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80" alt="Tenant">
                        <div>
                            <strong>Arafatullah Rifat</strong>
                            <p>Mansion • Unit 2A</p>
                        </div>
                    </div>
                </div>
                <div class="maintenance-actions">
                    <button type="button" class="update-btn">Update Status ⌄</button>
                    <a class="view-link maint-view-link" href="#" data-title="Door Lock Replacement" data-tenant="Arafatullah Rifat" data-unit="Mansion • Unit 2A" data-msg="Front entrance digital lock malfunctioned. Serviced by technician." data-priority="Low">View Details</a>
                </div>
            </div>


            <div class="maintenance-card" data-status="pending">
                <div class="maintenance-main">
                    <div class="maintenance-status">
                        <span class="badge pending">◉ Pending</span>
                        <span>10h ago</span>
                    </div>
                    <h3>AC Gas Refill</h3>
                    <p class="maintenance-msg">Master bedroom AC unit is not blowing cool air. Needs refrigerant.</p>
                    <div class="maintenance-person">
                        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80" alt="Tenant">
                        <div>
                            <strong>Arafatullah Rifat</strong>
                            <p>Mansion • Unit 2A</p>
                        </div>
                    </div>
                </div>
                <div class="maintenance-actions">
                    <button type="button" class="update-btn">Update Status ⌄</button>
                    <a class="view-link maint-view-link" href="#" data-title="AC Gas Refill" data-tenant="Arafatullah Rifat" data-unit="Mansion • Unit 2A" data-msg="Master bedroom AC unit is not blowing cool air. Needs refrigerant." data-priority="Standard">View Details</a>
                </div>
            </div>


            <div class="maintenance-card" data-status="progress">
                <div class="maintenance-main">
                    <div class="maintenance-status">
                        <span class="badge progress">🔧 In Progress</span>
                        <span>Today</span>
                    </div>
                    <h3>Balcony Glass Fitting</h3>
                    <p class="maintenance-msg">Window slider rattling heavily during monsoon winds.</p>
                    <div class="maintenance-person">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&q=80" alt="Tenant">
                        <div>
                            <strong>Farhan Nadim</strong>
                            <p>Bachelor • Unit 9A</p>
                        </div>
                    </div>
                </div>
                <div class="maintenance-actions">
                    <button type="button" class="update-btn">Update Status ⌄</button>
                    <a class="view-link maint-view-link" href="#" data-title="Balcony Glass Fitting" data-tenant="Farhan Nadim" data-unit="Bachelor • Unit 9A" data-msg="Window slider rattling heavily during monsoon winds." data-priority="Standard">View Details</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>


<div class="pm-modal-overlay" id="maintDetailModal">
    <div class="pm-modal">
        <div class="pm-modal-header">
            <div class="pm-modal-title">Maintenance Ticket Details</div>
            <button type="button" class="pm-modal-close" onclick="document.getElementById('maintDetailModal').classList.remove('active')">&times;</button>
        </div>
        <div class="pm-modal-body">
            <div style="background: #f7f5f7; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                <h3 id="mdTitle" style="font-size: 18px; color: #0755cf; margin-bottom: 4px;">Issue Title</h3>
                <p id="mdTenant" style="font-size: 13px; color: #555;">Tenant Info</p>
            </div>

            <div style="margin-bottom: 18px;">
                <label style="font-size: 12px; font-weight: 700; color: #777; text-transform: uppercase;">Resident Description</label>
                <p id="mdMsg" style="font-size: 14px; color: #333; margin-top: 6px; line-height: 1.5;"></p>
            </div>

            <div style="margin-bottom: 18px;">
                <label style="font-size: 12px; font-weight: 700; color: #777; text-transform: uppercase;">Photo Attachment</label>
                <div style="margin-top: 8px;">
                    <img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=400&q=80" style="width: 100%; height: 160px; object-fit: cover; border-radius: 8px;">
                </div>
            </div>

            <div class="form-group-pm">
                <label>Assign Maintenance Staff</label>
                <select id="staffAssignee">
                    <option>Rahim Uddin (Plumbing Specialist)</option>
                    <option>Kamal Hossain (Electrician)</option>
                    <option>Hasan Ali (General Contractor)</option>
                </select>
            </div>
        </div>
        <div class="pm-modal-footer">
            <button type="button" class="primary-btn" onclick="alert('Technician assigned and task scheduled!'); document.getElementById('maintDetailModal').classList.remove('active');">Assign Technician</button>
        </div>
    </div>
</div>

<script src="../js/landlord.js"></script>
<script>

    function filterMaint(status) {
        document.querySelectorAll(".maintenance-card").forEach(card => {
            if (status === "all" || card.dataset.status === status) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    }


    const maintModal = document.getElementById("maintDetailModal");
    document.querySelectorAll(".maint-view-link").forEach(link => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            document.getElementById("mdTitle").textContent = link.dataset.title;
            document.getElementById("mdTenant").textContent = `${link.dataset.tenant} • ${link.dataset.unit} (${link.dataset.priority})`;
            document.getElementById("mdMsg").textContent = link.dataset.msg;
            maintModal.classList.add("active");
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