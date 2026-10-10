<?php
require_once __DIR__ . '/../config/db.php';

$properties = [];
if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->query("SELECT p.*, COALESCE(pi.image_url, 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?q=80&w=800') as image_url
                             FROM properties p
                             LEFT JOIN property_images pi ON p.property_id = pi.property_id AND pi.is_primary = 1
                             ORDER BY p.created_at DESC");
        $properties = $stmt->fetchAll();
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Properties | PropManage</title>

    <link rel="stylesheet" href="../css/properties.css">

</head>

<body>

    <header class="navbar">
        <div class="navbar-container">
            <a href="../index.php" class="logo">
                PropManage
            </a>
            <nav class="nav-menu">
                <a href="../index.php">Home</a>
                <a href="properties.php" class="active">Properties</a>
                <a href="#">About Us</a>
                <a href="#">Contact</a>
            </nav>
            <div class="nav-actions">
                <?php if (isUserLoggedIn()):
                    $currRole = $_SESSION['role'] ?? 'tenant';
                    $dashUrl = $currRole === 'landlord' ? '../landlord/dashboard.php' : ($currRole === 'admin' ? '../admin/dashboard.php' : '../tenant/explore.php');
                ?>
                    <a href="<?php echo $dashUrl; ?>" class="login-btn" style="background:#0755cf;">
                        Dashboard (<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Account'); ?>)
                    </a>
                <?php else: ?>
                    <a href="../auth/login.php" class="login-btn">
                        Sign In / Sign Up
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="page-header">

        <div class="page-header-container">

            <h1>
                Explore Properties
            </h1>

            <p>
                Sub Heading
            </p>

        </div>

    </section>

    <section class="search-section">

        <div class="search-container">

            <div class="search-field">
                <input type="text" id="location" placeholder="Location">
            </div>

            <div class="search-field">
                <select id="propertyType">
                    <option value="">Type</option>
                    <option value="Apartment">Apartment</option>
                    <option value="House">House</option>
                    <option value="Room">Room</option>
                    <option value="Commercial">Commercial</option>
                </select>
            </div>

            <div class="search-field">
                <select id="budget">
                    <option value="">Price Range</option>
                    <option value="10000">10,000 Tk</option>
                    <option value="20000">20,000 Tk</option>
                    <option value="30000">30,000 Tk</option>
                    <option value="50000">50,000+ Tk</option>
                </select>
            </div>

            <button class="search-btn"id="searchBtn">
                Search
            </button>

        </div>
    </section>

    <section class="properties">
        <div class="section-container">
            <div class="section-header">
                <div>
                    <h2>All Properties</h2>
                </div>
                <div class="sort-container">
                    <label for="sortBy">Sort By:</label>
                    <select id="sortBy" class="sort-select">
                        <option value="recommended">Recommended</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="newest">Newest First</option>
                    </select>
                </div>
            </div>

            <div class="filter-bar">
                <div class="filter-pills">
                    <button class="pill active" data-category="all">All</button>
                    <button class="pill" data-category="apartments">Apartments</button>
                    <button class="pill" data-category="houses">Houses</button>
                    <button class="pill" data-category="rooms">Rooms</button>
                    <button class="pill" data-category="commercial">Commercial</button>
                </div>
            </div>

            <div class="property-grid" id="propertyGrid">
                <?php if (!empty($properties)): ?>
                    <?php foreach ($properties as $prop):
                        $rawType = strtolower($prop['property_type']);
                        $cats = [$rawType];
                        if ($rawType === 'apartment') { $cats[] = 'apartments'; }
                        if ($rawType === 'house' || $rawType === 'mansion') { $cats[] = 'houses'; }
                        if ($rawType === 'room' || $rawType === 'bachelor') { $cats[] = 'rooms'; }
                        if ($rawType === 'commercial') { $cats[] = 'commercial'; }
                        $catAttr = implode(' ', $cats);
                    ?>
                        <div class="property-card" data-category="<?php echo htmlspecialchars($catAttr); ?>" data-price="<?php echo $prop['rent_amount']; ?>" data-location="<?php echo htmlspecialchars($prop['address']); ?>">
                            <div class="property-image">
                                <img src="<?php echo htmlspecialchars($prop['image_url']); ?>" alt="<?php echo htmlspecialchars($prop['title']); ?>">
                                <span class="available-badge"><?php echo strtoupper($prop['status']); ?></span>
                            </div>
                            <div class="property-details">
                                <div class="card-header-row">
                                    <div class="title-container">
                                        <h3><?php echo htmlspecialchars($prop['title']); ?></h3>
                                        <p class="location">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <?php echo htmlspecialchars($prop['address']); ?>
                                        </p>
                                    </div>
                                    <div class="price-container">
                                        <span class="price"><?php echo number_format($prop['rent_amount']); ?> Tk</span>
                                        <span class="per-month">/mo</span>
                                    </div>
                                </div>
                                <div class="features">
                                    <span><?php echo $prop['bedrooms']; ?> Beds</span>
                                    <span><?php echo $prop['bathrooms']; ?> Baths</span>
                                    <span><?php echo $prop['size_sqft']; ?> sqft</span>
                                </div>
                                <a href="property-details.php?id=<?php echo $prop['property_id']; ?>" class="btn-view-details">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; border: 1px dashed #dbe5f5;">
                        <p style="font-size: 18px; color: #40424c; font-weight: 600; margin-bottom: 8px;">No properties currently available in the database.</p>
                        <p style="color: #777; font-size: 14px;">Properties added by landlords will appear here automatically.</p>
                        <a href="../landlord/add-property.php" class="btn-view-details" style="display: inline-block; width: auto; padding: 10px 24px; margin-top: 15px;">+ Add Property as Landlord</a>
                    </div>
                <?php endif; ?>

            </div>


            <div id="noResults" class="no-results">
                <h3>No properties found</h3>
                <p>Try changing your search or filter.</p>
            </div>

        </div>

    </section>

    <footer>
        <div class="footer-container">
            <div class="footer-brand">
                <h3>PropManage</h3>
                <p>Sub Heading</p>
            </div>


            <div class="footer-column">
                <h4>Company</h4>
                <a href="../index.php">Home</a>
                <a href="#">About</a>
                <a href="#">Contact</a>
                <a href="#">Privacy Policy</a>
            </div>

            <div class="footer-column">
                <h4>Account</h4>
                <a href="../auth/login.php">Sign In</a>
                <a href="../auth/register.php">Sign Up</a>
            </div>

        </div>

        <div class="copyright">
            <p>
                © 2026 PropManage. Made by Farhan Nadim, Mashfi Rahman, Arafatullah Rifat & Fariha Akter Prity.
            </p>
        </div>
    </footer>

    <script src="../js/properties.js"></script>

</body>
</html>