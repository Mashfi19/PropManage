<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'Md Farhan Nadim';
$tenants = [];
$tenantCount = 4;

if ($db_connected && $pdo) {
    try {
        $cStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'tenant'");
        $tenantCount = $cStmt->fetchColumn() ?: 4;

        $stmt = $pdo->query("SELECT u.*, COALESCE(tp.profession, 'Resident') as profession,
                                COALESCE(p.title, 'Family Flat') as property_title,
                                COALESCE(p.address, '7/B Banani') as address,
                                COALESCE(b.monthly_rent, 25000) as rent,
                                COALESCE(b.start_date, '2026-01-01') as lease_start,
                                COALESCE(b.end_date, '2026-12-31') as lease_end
                             FROM users u
                             LEFT JOIN tenant_profiles tp ON u.user_id = tp.user_id
                             LEFT JOIN bookings b ON u.user_id = b.tenant_id
                             LEFT JOIN properties p ON b.property_id = p.property_id
                             WHERE u.role = 'tenant'
                             GROUP BY u.user_id
                             ORDER BY u.created_at DESC");
        $tenants = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Tenants | PropManage</title>
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
        <a class="side-link active" href="tenants.php"><span class="side-icon">♧</span>Tenants</a>
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
        <div class="stat-card stat-wide" style="margin-bottom:48px">
            <div class="stat-top">TOTAL TENANTS <span class="stat-icon gray">♟</span></div>
            <div class="stat-number"><?php echo $tenantCount; ?></div>
        </div>

        <div class="tenant-head">
            <div>
                <h2>Tenant Directory</h2>
                <p>Manage all current residents and leases.</p>
            </div>
            <div class="tenant-tools">
                <input class="table-search" placeholder="⌕  Search tenant">
                <button type="button" class="primary-btn" id="openAddTenantBtn">
                    ＋ Add Tenant
                </button>
            </div>
        </div>


        <section class="panel table-panel">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Property / Unit</th>
                        <th>Monthly Rent</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tenants)): ?>
                        <?php foreach ($tenants as $t): ?>
                            <tr>
                                <td>
                                    <div class="person">
                                        <img src="<?php echo htmlspecialchars($t['profile_image']); ?>">
                                        <div>
                                            <div class="person-name"><?php echo htmlspecialchars($t['full_name']); ?></div>
                                            <div class="person-email"><?php echo htmlspecialchars($t['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($t['property_title']); ?><br><small><?php echo htmlspecialchars($t['address']); ?></small></td>
                                <td><b><?php echo number_format($t['rent']); ?> BDT</b></td>
                                <td><b style="color: #009664;">Active</b></td>
                                <td><button type="button" class="details-btn tenant-details-btn"
                                    data-name="<?php echo htmlspecialchars($t['full_name']); ?>"
                                    data-unit="<?php echo htmlspecialchars($t['property_title']); ?>"
                                    data-rent="<?php echo number_format($t['rent']); ?> BDT"
                                    data-phone="<?php echo htmlspecialchars($t['phone']); ?>"
                                    data-lease="<?php echo date('M Y', strtotime($t['lease_start'])) . ' - ' . date('M Y', strtotime($t['lease_end'])); ?>">Details</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=100&q=80">
                                <div>
                                    <div class="person-name">Mashfi Rahman</div>
                                    <div class="person-email">mashfi@gmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td>Family<br>7/B</td>
                        <td><b>21,000 BDT</b></td>
                        <td><b style="color: #009664;">Active</b></td>
                        <td><button type="button" class="details-btn tenant-details-btn" data-name="Mashfi Rahman" data-unit="Family Flat (Unit 7B)" data-rent="21,000 BDT" data-phone="01712-345678" data-lease="Jan 2026 - Dec 2026">Details</button></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&q=80">
                                <div>
                                    <div class="person-name">Farhan Nadim</div>
                                    <div class="person-email">fanadim@gmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td>Bachelor<br>9/A</td>
                        <td><b>27,000 BDT</b></td>
                        <td><b style="color: #009664;">Active</b></td>
                        <td><button type="button" class="details-btn tenant-details-btn" data-name="Farhan Nadim" data-unit="Bachelor Unit (Unit 9A)" data-rent="27,000 BDT" data-phone="01708-375128" data-lease="Mar 2026 - Feb 2027">Details</button></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80">
                                <div>
                                    <div class="person-name">Arafatullah Rifat</div>
                                    <div class="person-email">rifat@gmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td>Mansion<br>2/A,B,C</td>
                        <td><b>50,000 BDT</b></td>
                        <td><b style="color: #009664;">Active</b></td>
                        <td><button type="button" class="details-btn tenant-details-btn" data-name="Arafatullah Rifat" data-unit="Mansion (Unit 2A,B,C)" data-rent="50,000 BDT" data-phone="01755-998877" data-lease="Aug 2025 - Jul 2027">Details</button></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="person">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80">
                                <div>
                                    <div class="person-name">Fariha Akter Prity</div>
                                    <div class="person-email">fariha@gmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td>Bachelor<br>9/C</td>
                        <td><b>25,000 BDT</b></td>
                        <td><b style="color: #009664;">Active</b></td>
                        <td><button type="button" class="details-btn tenant-details-btn" data-name="Fariha Akter Prity" data-unit="Bachelor (Unit 9C)" data-rent="25,000 BDT" data-phone="01811-223344" data-lease="Jun 2026 - May 2027">Details</button></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="table-footer">
                <span>Showing 1 to 4</span>
                <span>Previous　 <b>1</b>　2　3　…　 Next</span>
            </div>
        </section>
    </main>
</div>


<div class="pm-modal-overlay" id="addTenantModal">
    <div class="pm-modal">
        <div class="pm-modal-header">
            <div class="pm-modal-title">Add New Tenant</div>
            <button type="button" class="pm-modal-close" onclick="document.getElementById('addTenantModal').classList.remove('active')">&times;</button>
        </div>
        <form id="addTenantForm" onsubmit="event.preventDefault(); alert('New resident onboarded successfully! Lease agreement issued.'); document.getElementById('addTenantModal').classList.remove('active');">
            <div class="pm-modal-body">
                <div class="form-group-pm">
                    <label>Tenant Full Name</label>
                    <input type="text" placeholder="e.g. Tanvir Hossain" required>
                </div>
                <div class="form-group-pm">
                    <label>Tenant Email</label>
                    <input type="email" placeholder="e.g. tanvir@example.com" required>
                </div>
                <div class="form-group-pm">
                    <label>Phone Number</label>
                    <input type="tel" placeholder="e.g. 01700-000000" required>
                </div>
                <div class="form-row-2">
                    <div class="form-group-pm">
                        <label>Assigned Property / Unit</label>
                        <select required>
                            <option>Family Flat - 7B</option>
                            <option>Bachelor Suite - 9A</option>
                            <option>Mansion - Unit 2A</option>
                            <option>Bachelor Flat - 9C</option>
                        </select>
                    </div>
                    <div class="form-group-pm">
                        <label>Monthly Rent (BDT)</label>
                        <input type="number" value="25000" required>
                    </div>
                </div>
            </div>
            <div class="pm-modal-footer">
                <button type="button" class="outline-btn" onclick="document.getElementById('addTenantModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="primary-btn">Add Resident</button>
            </div>
        </form>
    </div>
</div>


<div class="pm-modal-overlay" id="tenantDetailsModal">
    <div class="pm-modal">
        <div class="pm-modal-header">
            <div class="pm-modal-title">Resident Profile & Lease</div>
            <button type="button" class="pm-modal-close" onclick="document.getElementById('tenantDetailsModal').classList.remove('active')">&times;</button>
        </div>
        <div class="pm-modal-body">
            <div style="background: #f7f5f7; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                <h3 id="tdName" style="font-size: 17px; color: #25252a; margin-bottom: 4px;">Tenant Name</h3>
                <p id="tdUnit" style="font-size: 13px; color: #0755cf; font-weight: 600;">Assigned Unit</p>
            </div>
            <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f0edf0; padding-bottom: 8px;">
                    <span>Contact Phone:</span>
                    <strong id="tdPhone">Phone</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f0edf0; padding-bottom: 8px;">
                    <span>Monthly Rent:</span>
                    <strong id="tdRent" style="color: #0755cf;">Rent BDT</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Lease Period:</span>
                    <strong id="tdLease">Period</strong>
                </div>
            </div>
        </div>
        <div class="pm-modal-footer">
            <a href="messages.php" class="outline-btn" style="text-decoration:none;">Message Tenant</a>
            <button type="button" class="primary-btn" onclick="document.getElementById('tenantDetailsModal').classList.remove('active')">Close</button>
        </div>
    </div>
</div>

<script src="../js/landlord.js"></script>
<script>

    const addTenantBtn = document.getElementById("openAddTenantBtn");
    const addTenantModal = document.getElementById("addTenantModal");
    if (addTenantBtn && addTenantModal) {
        addTenantBtn.addEventListener("click", () => addTenantModal.classList.add("active"));
    }


    const tdModal = document.getElementById("tenantDetailsModal");
    document.querySelectorAll(".tenant-details-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("tdName").textContent = btn.dataset.name;
            document.getElementById("tdUnit").textContent = btn.dataset.unit;
            document.getElementById("tdRent").textContent = btn.dataset.rent;
            document.getElementById("tdPhone").textContent = btn.dataset.phone;
            document.getElementById("tdLease").textContent = btn.dataset.lease;
            tdModal.classList.add("active");
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