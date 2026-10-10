<?php
require_once __DIR__ . '/../config/db.php';

$propId = intval($_GET['id'] ?? 1);
$prop = [
    'property_id'   => 1,
    'title'         => 'Modern 3-Bed Apartment',
    'property_type' => 'Apartment',
    'address'       => '3/P, Banani, Dhaka',
    'rent_amount'   => 25000,
    'bedrooms'      => 3,
    'bathrooms'     => 2,
    'size_sqft'     => 1250,
    'description'   => 'Beautiful modern apartment equipped with all modern amenities.',
    'landlord_name' => 'Md Farhan Nadim',
    'landlord_email'=> 'landlord@propmanage.com',
    'landlord_phone'=> '01708-375128',
    'image_url'     => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?q=80&w=1200&auto=format&fit=crop'
];

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, u.full_name as landlord_name, u.email as landlord_email, u.phone as landlord_phone, u.profile_image as landlord_avatar,
                                COALESCE(pi.image_url, 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?q=80&w=1200') as image_url
                               FROM properties p
                               JOIN users u ON p.landlord_id = u.user_id
                               LEFT JOIN property_images pi ON p.property_id = pi.property_id AND pi.is_primary = 1
                               WHERE p.property_id = :pid LIMIT 1");
        $stmt->execute([':pid' => $propId]);
        $row = $stmt->fetch();
        if ($row) {
            $prop = array_merge($prop, $row);
        }
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?php echo htmlspecialchars($prop['title']); ?> | PropManage</title>

    <link
        rel="stylesheet"
        href="../css/property-details.css"
    >

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


    <main class="property-page">

        <div class="property-container">


            <a
                href="properties.php"
                class="back-link"
            >
                ← Back to Properties
            </a>


            <div class="property-content">


                <div class="property-left">


                    <div class="main-image">

                        <img
                            id="mainPropertyImage"
                            src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?q=80&w=1200&auto=format&fit=crop"
                            alt="Modern Apartment"
                        >

                        <span class="available-badge">
                            AVAILABLE
                        </span>

                    </div>


                    <div class="thumbnail-container">


                        <button
                            class="thumbnail active"
                            data-image="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?q=80&w=1200&auto=format&fit=crop"
                        >

                            <img
                                src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?q=80&w=400&auto=format&fit=crop"
                                alt="Property image 1"
                            >

                        </button>


                        <button
                            class="thumbnail"
                            data-image="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?q=80&w=1200&auto=format&fit=crop"
                        >

                            <img
                                src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?q=80&w=400&auto=format&fit=crop"
                                alt="Property image 2"
                            >

                        </button>


                        <button
                            class="thumbnail"
                            data-image="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?q=80&w=1200&auto=format&fit=crop"
                        >

                            <img
                                src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?q=80&w=400&auto=format&fit=crop"
                                alt="Property image 3"
                            >

                        </button>


                        <button
                            class="thumbnail"
                            data-image="https://images.unsplash.com/photo-1600607688969-a5bfcd646154?q=80&w=1200&auto=format&fit=crop"
                        >

                            <img
                                src="https://images.unsplash.com/photo-1600607688969-a5bfcd646154?q=80&w=400&auto=format&fit=crop"
                                alt="Property image 4"
                            >

                        </button>


                    </div>


                    <div class="description-section">

                        <h2>
                            About This Property
                        </h2>

                        <p>
                            Description.
                        </p>

                    </div>


                    <div class="features-section">

                        <h2>
                            Property Features
                        </h2>


                        <div class="feature-grid">


                            <div class="feature-item">


                                <div>

                                    <strong>
                                        Bedrooms
                                    </strong>

                                    <p>
                                        3 Bedrooms
                                    </p>

                                </div>

                            </div>


                            <div class="feature-item">


                                <div>

                                    <strong>
                                        Bathrooms
                                    </strong>

                                    <p>
                                        2 Bathrooms
                                    </p>

                                </div>

                            </div>


                            <div class="feature-item">


                                <div>

                                    <strong>
                                        Property Size
                                    </strong>

                                    <p>
                                        1250 sqft
                                    </p>

                                </div>

                            </div>


                            <div class="feature-item">


                                <div>

                                    <strong>
                                        Property Type
                                    </strong>

                                    <p>
                                        Apartment
                                    </p>

                                </div>

                            </div>


                            <div class="feature-item">


                                <div>

                                    <strong>
                                        Parking
                                    </strong>

                                    <p>
                                        Available
                                    </p>

                                </div>

                            </div>


                            <div class="feature-item">


                                <div>

                                    <strong>
                                        Elevator
                                    </strong>

                                    <p>
                                        Available
                                    </p>

                                </div>

                            </div>


                        </div>

                    </div>


                </div>


                <aside class="property-sidebar">


                    <div class="property-info-card">


                        <div class="property-title-row">

                            <div>

                                <span class="property-type">
                                    <?php echo strtoupper($prop['property_type']); ?>
                                </span>

                                <h1>
                                    <?php echo htmlspecialchars($prop['title']); ?>
                                </h1>

                                <p class="property-location">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    <?php echo htmlspecialchars($prop['address']); ?>
                                </p>
                            </div>
                        </div>


                        <div class="detail-price">
                            <span>
                                <?php echo number_format($prop['rent_amount']); ?> Tk
                            </span>
                            <small>/ month</small>
                        </div>


                        <div class="quick-info">
                            <div>
                                <strong><?php echo $prop['bedrooms']; ?></strong>
                                <span>Beds</span>
                            </div>
                            <div>
                                <strong><?php echo $prop['bathrooms']; ?></strong>
                                <span>Baths</span>
                            </div>
                            <div>
                                <strong><?php echo $prop['size_sqft']; ?></strong>
                                <span>sqft</span>
                            </div>
                        </div>


                        <button class="rent-btn" id="rentBtn">
                            Request to Rent
                        </button>

                        <p class="login-note">
                            Quickly apply with your desired move-in date and lease term.
                        </p>
                    </div>


                    <div class="landlord-card">
                        <h3>Property Owner</h3>
                        <div class="landlord-info">
                            <div class="landlord-avatar">
                                <?php echo strtoupper(substr($prop['landlord_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <strong><?php echo htmlspecialchars($prop['landlord_name']); ?></strong>
                                <p><?php echo htmlspecialchars($prop['landlord_phone']); ?></p>
                            </div>
                        </div>


                        <button
                            class="message-btn"
                            id="messageBtn"
                        >
                            Message Landlord
                        </button>

                    </div>


                    <div class="location-card">

                        <h3>
                            Location
                        </h3>


                        <div class="map-placeholder">

                            <div class="map-icon">
                                📍
                            </div>

                            <p>
                                Dhanmondi, Dhaka
                            </p>

                        </div>

                    </div>


                </aside>


            </div>

        </div>

    </main>

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

    <script src="../js/property-details.js"></script>

</body>
</html>