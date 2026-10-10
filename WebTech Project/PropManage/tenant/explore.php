<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'Mashfi Rahman';
$exploreProps = [];

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->query("SELECT p.*, COALESCE(pi.image_url, 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=900') as image_url
                             FROM properties p
                             LEFT JOIN property_images pi ON p.property_id = pi.property_id AND pi.is_primary = 1
                             WHERE p.status = 'available'
                             ORDER BY p.created_at DESC");
        $exploreProps = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Properties | PropManage</title>
    <link rel="stylesheet" href="../css/tenant.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>


<aside class="sidebar">
    <a href="../index.php" class="brand">
        <span class="brand-mark">◈</span>PropManage
    </a>
    <nav class="side-nav">
        <a class="side-link active" href="explore.php"><span class="side-icon">▦</span>Explore</a>
        <a class="side-link" href="my-requests.php"><span class="side-icon">♧</span>My Requests</a>
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
            <input type="text" placeholder="Search properties or locations...">
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
            <h1 class="page-title">Explore</h1>
        </div>

        <div class="filters">
            <div class="segment">
                <button class="active">All</button>
                <button>Available</button>
                <button>Rented</button>
            </div>
            <select class="filter-select">
                <option value="">Type</option>
                <option value="Apartment">Apartment</option>
                <option value="Family">Family</option>
                <option value="Bachelor">Bachelor</option>
                <option value="Mansion">Mansion</option>
            </select>
            <select class="filter-select">
                <option value="">Sort By</option>
                <option value="low">Price: Low to High</option>
                <option value="high">Price: High to Low</option>
                <option value="new">Newest First</option>
            </select>
        </div>


        <div class="property-grid">
            <?php if (!empty($exploreProps)): ?>
                <?php foreach ($exploreProps as $p): ?>
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
                            <div class="tenant-card-actions">
                                <a href="../properties/property-details.php?id=<?php echo $p['property_id']; ?>" class="btn-details">Details</a>
                                <button type="button" class="btn-proceed"
                                    data-id="<?php echo $p['property_id']; ?>"
                                    data-title="<?php echo htmlspecialchars($p['title']); ?>"
                                    data-price="<?php echo number_format($p['rent_amount']); ?> BDT">Proceed</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px 20px; background: #fff; border-radius: 12px; border: 1px dashed #dedede;">
                    <p style="font-size: 16px; color: #555; margin-bottom: 8px;">No properties are currently available for rent.</p>
                    <p style="color: #888; font-size: 13px;">Please check back soon for newly listed units in Dhaka.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>


<div class="pm-modal-overlay" id="rentRequestModal">
    <div class="pm-modal">
        <div class="pm-modal-header">
            <div class="pm-modal-title">Submit Rental Request</div>
            <button type="button" class="pm-modal-close" id="closeRentModal">&times;</button>
        </div>
        <form id="rentRequestForm">
            <div class="pm-modal-body">
                <div style="background: #f4f6fb; padding: 14px; border-radius: 8px; margin-bottom: 20px;">
                    <strong id="rentModalPropertyTitle" style="display:block; font-size: 16px; color: #0755cf;">Selected Property</strong>
                    <span id="rentModalPrice" style="font-size: 14px; font-weight: 700; color: #333;">25,000 BDT /mo</span>
                </div>

                <div class="form-group-pm">
                    <label>Preferred Move-in Date</label>
                    <input type="date" required value="2026-10-01">
                </div>

                <div class="form-group-pm">
                    <label>Lease Duration (Months)</label>
                    <select required>
                        <option value="6">6 Months</option>
                        <option value="12" selected>12 Months (1 Year)</option>
                        <option value="24">24 Months (2 Years)</option>
                    </select>
                </div>

                <div class="form-group-pm">
                    <label>Number of Occupants</label>
                    <input type="number" min="1" max="10" value="2" required>
                </div>

                <div class="form-group-pm">
                    <label>Message to Landlord (Optional)</label>
                    <textarea class="form-control-textarea" style="height: 90px; margin-bottom: 0;" placeholder="Introduce yourself, job status, or special requirements..."></textarea>
                </div>
            </div>
            <div class="pm-modal-footer">
                <button type="button" class="outline-btn" onclick="document.getElementById('rentRequestModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="primary-btn">Submit Request</button>
            </div>
        </form>
    </div>
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
