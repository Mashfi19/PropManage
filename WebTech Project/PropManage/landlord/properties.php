<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'Md Farhan Nadim';
$userId = $_SESSION['user_id'] ?? 2;

$landlordProps = [];
if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, COALESCE(pi.image_url, 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=900') as image_url
                               FROM properties p
                               LEFT JOIN property_images pi ON p.property_id = pi.property_id AND pi.is_primary = 1
                               WHERE p.landlord_id = :lid
                               ORDER BY p.created_at DESC");
        $stmt->execute([':lid' => $userId]);
        $landlordProps = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Properties | PropManage</title>
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
        <a class="side-link active" href="properties.php"><span class="side-icon">▦</span>Properties</a>
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
            <input type="text" placeholder="Search properties...">
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
            <h1 class="page-title">Properties</h1>
            <a href="add-property.php" class="primary-btn" style="text-decoration:none;">
                ＋ Add Property
            </a>
        </div>


        <div class="filters">
            <div class="segment">
                <button type="button" class="active">All</button>
                <button type="button">Available</button>
                <button type="button">Rented</button>
            </div>
            <select class="filter-select">
                <option>Type</option>
                <option>Apartment</option>
                <option>House</option>
                <option>Commercial</option>
            </select>
            <select class="filter-select">
                <option>Sort By</option>
                <option>Price: Low to High</option>
                <option>Price: High to Low</option>
            </select>
        </div>


        <div class="property-grid">
            <?php if (!empty($landlordProps)): ?>
                <?php foreach ($landlordProps as $p): ?>
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
                            <div class="card-actions">
                                <button type="button" class="edit-prop-btn"
                                    data-title="<?php echo htmlspecialchars($p['title']); ?>"
                                    data-price="<?php echo $p['rent_amount']; ?>"
                                    data-location="<?php echo htmlspecialchars($p['address']); ?>"
                                    data-beds="<?php echo $p['bedrooms']; ?>"
                                    data-baths="<?php echo $p['bathrooms']; ?>"
                                    data-size="<?php echo $p['size_sqft']; ?>">Edit</button>
                                <button type="button" class="photos-prop-btn" data-title="<?php echo htmlspecialchars($p['title']); ?>">Photos</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px 20px; background: #fff; border-radius: 12px; border: 1px dashed #dedede;">
                    <p style="font-size: 16px; color: #555; margin-bottom: 12px;">You haven't listed any properties yet.</p>
                    <a href="add-property.php" class="primary-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">+ Add Your First Property</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>


<div class="pm-modal-overlay" id="editPropertyModal">
    <div class="pm-modal">
        <div class="pm-modal-header">
            <div class="pm-modal-title">Edit Property Details</div>
            <button type="button" class="pm-modal-close" onclick="document.getElementById('editPropertyModal').classList.remove('active')">&times;</button>
        </div>
        <form id="editPropertyForm" onsubmit="event.preventDefault(); alert('Property listing updated successfully!'); document.getElementById('editPropertyModal').classList.remove('active');">
            <div class="pm-modal-body">
                <div class="form-group-pm">
                    <label>Title</label>
                    <input type="text" id="editPropTitle" required>
                </div>
                <div class="form-group-pm">
                    <label>Address / Location</label>
                    <input type="text" id="editPropLocation" required>
                </div>
                <div class="form-row-2">
                    <div class="form-group-pm">
                        <label>Monthly Rent (BDT)</label>
                        <input type="number" id="editPropPrice" required>
                    </div>
                    <div class="form-group-pm">
                        <label>Size (sqft)</label>
                        <input type="number" id="editPropSize" required>
                    </div>
                </div>
                <div class="form-row-2">
                    <div class="form-group-pm">
                        <label>Bedrooms</label>
                        <input type="number" id="editPropBeds" required>
                    </div>
                    <div class="form-group-pm">
                        <label>Bathrooms</label>
                        <input type="number" id="editPropBaths" required>
                    </div>
                </div>
            </div>
            <div class="pm-modal-footer">
                <button type="button" class="outline-btn" onclick="document.getElementById('editPropertyModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="primary-btn">Save Changes</button>
            </div>
        </form>
    </div>
</div>


<div class="pm-modal-overlay" id="photosModal">
    <div class="pm-modal modal-lg">
        <div class="pm-modal-header">
            <div class="pm-modal-title" id="photosModalTitle">Property Photos</div>
            <button type="button" class="pm-modal-close" onclick="document.getElementById('photosModal').classList.remove('active')">&times;</button>
        </div>
        <div class="pm-modal-body">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px;">
                <img src="https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=600&q=80" style="width:100%; height:130px; object-fit:cover; border-radius:8px;">
                <img src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=600&q=80" style="width:100%; height:130px; object-fit:cover; border-radius:8px;">
                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=600&q=80" style="width:100%; height:130px; object-fit:cover; border-radius:8px;">
            </div>
            <div class="photo-attachment-dropzone" onclick="alert('Select new images to upload...')">
                <div class="dropzone-icon">📷</div>
                <div class="dropzone-text">Upload additional photos</div>
                <div class="dropzone-hint">Supports JPG, PNG up to 10MB</div>
            </div>
        </div>
        <div class="pm-modal-footer">
            <button type="button" class="primary-btn" onclick="document.getElementById('photosModal').classList.remove('active')">Done</button>
        </div>
    </div>
</div>

<script src="../js/landlord.js"></script>
<script>

    document.querySelectorAll(".edit-prop-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("editPropTitle").value = btn.dataset.title;
            document.getElementById("editPropLocation").value = btn.dataset.location;
            document.getElementById("editPropPrice").value = btn.dataset.price;
            document.getElementById("editPropSize").value = btn.dataset.size;
            document.getElementById("editPropBeds").value = btn.dataset.beds;
            document.getElementById("editPropBaths").value = btn.dataset.baths;
            document.getElementById("editPropertyModal").classList.add("active");
        });
    });


    document.querySelectorAll(".photos-prop-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("photosModalTitle").textContent = btn.dataset.title + " - Photos";
            document.getElementById("photosModal").classList.add("active");
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