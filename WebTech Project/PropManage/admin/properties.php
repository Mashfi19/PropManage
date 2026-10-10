<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'System Administrator';
$adminProps = [];

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->query("SELECT p.*, COALESCE(pi.image_url, 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=900') as image_url,
                                    u.full_name as landlord_name
                             FROM properties p
                             LEFT JOIN property_images pi ON p.property_id = pi.property_id AND pi.is_primary = 1
                             JOIN users u ON p.landlord_id = u.user_id
                             ORDER BY p.created_at DESC");
        $adminProps = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Manage | PropManage</title>
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
        <a class="side-link active" href="properties.php"><span class="side-icon">🏢</span>Property Manage</a>
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
            <input type="text" placeholder="Search property listings...">
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
            <h1 class="page-title">Property Manage</h1>
            <a href="../landlord/add-property.php" class="primary-btn">
                ＋ Add Property
            </a>
        </div>


        <div class="filters">
            <div class="segment">
                <button type="button" class="active">All</button>
                <button type="button">Verified</button>
                <button type="button">Pending Audit</button>
            </div>
            <select class="filter-select">
                <option>More Filters</option>
                <option>Price Range</option>
                <option>Location Area</option>
                <option>Flagged Properties</option>
            </select>
        </div>


        <div class="property-grid">
            <?php if (!empty($adminProps)): ?>
                <?php foreach ($adminProps as $p): ?>
                    <div class="property-card">
                        <img class="property-img" src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>">
                        <div class="property-body">
                            <div class="property-top">
                                <div>
                                    <div class="property-name"><?php echo htmlspecialchars($p['title']); ?></div>
                                    <div class="property-location">⌖ <?php echo htmlspecialchars($p['address']); ?></div>
                                </div>
                                <div class="property-price"><?php echo number_format($p['rent_amount']); ?> BDT<small>/mo</small></div>
                            </div>
                            <div class="property-features">
                                <span>▱ <?php echo $p['bedrooms']; ?> Beds</span>
                                <span>♨ <?php echo $p['bathrooms']; ?> Baths</span>
                                <span>◩ <?php echo $p['size_sqft']; ?> sqft</span>
                            </div>
                            <div class="admin-card-actions">
                                <button type="button" class="btn-manage" onclick="alert('Managing property ID #<?php echo $p['property_id']; ?>: <?php echo htmlspecialchars($p['title']); ?>')">Manage</button>
                                <button type="button" class="btn-audit" data-title="<?php echo htmlspecialchars($p['title']); ?>" data-loc="<?php echo htmlspecialchars($p['address']); ?>">Audit</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="property-card">
                <img class="property-img" src="https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=900&q=80&auto=format&fit=crop" alt="Family Property">
                <div class="property-body">
                    <div class="property-top">
                        <div>
                            <div class="property-name">Family</div>
                            <div class="property-location">⌖ 3/P, Banani, Dhaka</div>
                        </div>
                        <div class="property-price">25,000 BDT<small>/mo</small></div>
                    </div>
                    <div class="property-features">
                        <span>▱ 3 Beds</span>
                        <span>♨ 3 Baths</span>
                        <span>◩ 1100 sqft</span>
                    </div>
                    <div class="admin-card-actions">
                        <button type="button" class="btn-manage" onclick="alert('Opening Admin Property Management panel for Family (3/P Banani)')">Manage</button>
                        <button type="button" class="btn-audit">Audit</button>
                    </div>
                </div>
            </div>

            <div class="property-card">
                <img class="property-img" src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?w=900&q=80&auto=format&fit=crop" alt="Bachelor Property">
                <div class="property-body">
                    <div class="property-top">
                        <div>
                            <div class="property-name">Bachelor</div>
                            <div class="property-location">⌖ 101/F, Bashundhara, Dhaka</div>
                        </div>
                        <div class="property-price">22,000 BDT<small>/mo</small></div>
                    </div>
                    <div class="property-features">
                        <span>▱ 3 Beds</span>
                        <span>♨ 3 Baths</span>
                        <span>◩ 1200 sqft</span>
                    </div>
                    <div class="admin-card-actions">
                        <button type="button" class="btn-manage" onclick="alert('Opening Admin Property Management panel for Bachelor (101/F Bashundhara)')">Manage</button>
                        <button type="button" class="btn-audit">Audit</button>
                    </div>
                </div>
            </div>

            <div class="property-card">
                <img class="property-img" src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=900&q=80&auto=format&fit=crop" alt="Bachelor Flat">
                <div class="property-body">
                    <div class="property-top">
                        <div>
                            <div class="property-name">Bachelor</div>
                            <div class="property-location">⌖ 1204 Gulshan, Dhaka</div>
                        </div>
                        <div class="property-price">19,000 BDT<small>/mo</small></div>
                    </div>
                    <div class="property-features">
                        <span>▱ 2 Beds</span>
                        <span>♨ 2 Baths</span>
                        <span>◩ 1000 sqft</span>
                    </div>
                    <div class="admin-card-actions">
                        <button type="button" class="btn-manage" onclick="alert('Opening Admin Property Management panel for Bachelor (1204 Gulshan)')">Manage</button>
                        <button type="button" class="btn-audit">Audit</button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 36px; color: #777; font-size: 13px;">
            <span>Showing 1-3 of 3245 properties</span>
            <span>‹　›</span>
        </div>
    </main>
</div>


<div class="pm-modal-overlay" id="auditListingModal">
    <div class="pm-modal">
        <div class="pm-modal-header">
            <div class="pm-modal-title">Property Compliance Audit</div>
            <button type="button" class="pm-modal-close" id="closeAuditModal">&times;</button>
        </div>
        <div class="pm-modal-body">
            <div style="background: #f7f5f7; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                <h3 id="auditPropertyTitle" style="font-size: 17px; color: #0755cf; margin-bottom: 4px;">Family Flat</h3>
                <p id="auditPropertyLocation" style="font-size: 13px; color: #555;">Banani, Dhaka</p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0edf0; padding-bottom: 8px;">
                    <span>Landlord Ownership Verification</span>
                    <span style="color: #00a66f; font-weight: 700;">✓ Verified (NID & Title)</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0edf0; padding-bottom: 8px;">
                    <span>Safety & Fire Clearance</span>
                    <span style="color: #00a66f; font-weight: 700;">✓ Certified (RAJUK)</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0edf0; padding-bottom: 8px;">
                    <span>Pricing Index Compliance</span>
                    <span style="color: #00a66f; font-weight: 700;">Within Normal Range</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Photo Authenticity Check</span>
                    <span style="color: #00a66f; font-weight: 700;">Verified High-Res</span>
                </div>
            </div>
        </div>
        <div class="pm-modal-footer">
            <button type="button" class="outline-btn" id="btnFlagAudit" style="color: #d02020; border-color: #f7c5c5;">⚠ Flag Listing</button>
            <button type="button" class="primary-btn" id="btnApproveAudit">✓ Approve Listing</button>
        </div>
    </div>
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
