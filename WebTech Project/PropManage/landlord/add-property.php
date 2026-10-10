<?php
require_once __DIR__ . '/../config/db.php';

$userName = $_SESSION['user_name'] ?? 'Md Farhan Nadim';
$landlordId = $_SESSION['user_id'] ?? 2;
$statusMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_title'])) {
    $title = trim($_POST['property_title'] ?? '');
    $type = trim($_POST['property_type'] ?? 'Apartment');
    $rent = floatval($_POST['monthly_rent'] ?? 0);
    $address = trim($_POST['full_address'] ?? 'Dhaka, Bangladesh');
    $beds = intval($_POST['bedrooms'] ?? 3);
    $baths = intval($_POST['bathrooms'] ?? 2);
    $size = intval($_POST['square_feet'] ?? 1200);
    $description = trim($_POST['description'] ?? '');

    if ($db_connected && $pdo && $title && $rent) {
        try {
            $stmt = $pdo->prepare("INSERT INTO properties (landlord_id, title, description, property_type, rent_amount, bedrooms, bathrooms, size_sqft, address, status)
                                   VALUES (:lid, :title, :desc, :type, :rent, :beds, :baths, :size, :addr, 'available')");
            $stmt->execute([
                ':lid'   => $landlordId,
                ':title' => $title,
                ':desc'  => $description,
                ':type'  => $type,
                ':rent'  => $rent,
                ':beds'  => $beds,
                ':baths' => $baths,
                ':size'  => $size,
                ':addr'  => $address
            ]);
            $propId = $pdo->lastInsertId();


            $imgStmt = $pdo->prepare("INSERT INTO property_images (property_id, image_url, is_primary) VALUES (:pid, 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?w=900', 1)");
            $imgStmt->execute([':pid' => $propId]);


            $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action_type, details, ip_address) VALUES (:uid, 'New Property Listing', :details, :ip)");
            $logStmt->execute([':uid' => $landlordId, ':details' => "Property #$propId '$title' listed", ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);

            header("Location: properties.php?msg=created");
            exit();
        } catch (PDOException $e) {
            $statusMsg = "Database error: " . $e->getMessage();
        }
    } else {
        header("Location: properties.php?msg=demo_created");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Property | PropManage</title>

    <link rel="stylesheet" href="../css/landlord.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>


<aside class="sidebar">

    <a href="../index.php" class="brand">
        <span class="brand-mark">◈</span>
        PropManage
    </a>

    <nav class="side-nav">

        <a class="side-link" href="dashboard.php">
            <span class="side-icon">▦</span>
            Dashboard
        </a>

        <a class="side-link active" href="properties.php">
            <span class="side-icon">▦</span>
            Properties
        </a>

        <a class="side-link" href="rent-requests.php">
            <span class="side-icon">♧</span>
            Rent Requests
        </a>

        <a class="side-link" href="messages.php">
            <span class="side-icon">▣</span>
            Messages
        </a>

        <a class="side-link" href="tenants.php">
            <span class="side-icon">♧</span>
            Tenants
        </a>

        <a class="side-link" href="payments.php">
            <span class="side-icon">▣</span>
            Payments
        </a>

        <a class="side-link" href="maintenance.php">
            <span class="side-icon">🔧</span>
            Maintenance
        </a>

    </nav>

    <div class="settings">

        <a class="side-link" href="profile.php">
            <span class="side-icon">⚙</span>
            Settings
        </a>

    </div>

</aside>


<div class="main">


    <header class="topbar">

        <div class="global-search">
            <span>⌕</span>
            <input type="text" placeholder="Search">
        </div>

        <div class="profile user-dropdown-container" id="profileDropdownBtn">

            <div class="profile-name">
                <?php echo htmlspecialchars($userName); ?>
                <div class="profile-role">
                    Landlord
                </div>
            </div>

            <img
                class="avatar"
                src="<?php echo htmlspecialchars(getActiveUserAvatar()); ?>"
                alt="Landlord"
            >


            <div class="user-dropdown-menu" id="userMenu">
                <div class="dropdown-header">Switch Role Preview</div>
                <a class="dropdown-item" href="../tenant/explore.php"><span>🔑</span> Tenant Portal</a>
                <a class="dropdown-item" href="../admin/dashboard.php"><span>🛡️</span> Admin Portal</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="profile.php"><span>👤</span> Edit Profile</a>
                <a class="dropdown-item" href="../auth/login.php" style="color: #d02020;"><span>🚪</span> Log Out</a>
            </div>

        </div>

    </header>


    <main class="content">


        <div class="page-head">

            <div>

                <h1 class="page-title">
                    Add Property
                </h1>

                <p class="page-subtitle">
                    Add a new property to your rental listings.
                </p>

            </div>

            <a href="properties.php" class="secondary-btn">
                ← Back to Properties
            </a>

        </div>


        <form
            class="property-form"
            id="addPropertyForm"
            method="POST"
            action="add-property.php"
            enctype="multipart/form-data"
        >


            <section class="form-card">

                <div class="form-card-header">

                    <h2>
                        Basic Information
                    </h2>

                    <p>
                        Enter the basic information about your property.
                    </p>

                </div>


                <div class="form-grid">


                    <div class="form-group full">

                        <label for="propertyTitle">
                            Property Title
                        </label>

                        <input
                            type="text"
                            id="propertyTitle"
                            name="property_title"
                            placeholder="e.g. Modern Family Apartment"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="propertyType">
                            Property Type
                        </label>

                        <select
                            id="propertyType"
                            name="property_type"
                            required
                        >

                            <option value="">
                                Select property type
                            </option>

                            <option value="Family">
                                Family
                            </option>

                            <option value="Bachelor">
                                Bachelor
                            </option>

                            <option value="Mansion">
                                Mansion
                            </option>

                            <option value="Apartment">
                                Apartment
                            </option>

                            <option value="House">
                                House
                            </option>

                            <option value="Commercial">
                                Commercial
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="monthlyRent">
                            Monthly Rent
                        </label>

                        <div class="input-with-suffix">

                            <input
                                type="number"
                                id="monthlyRent"
                                name="monthly_rent"
                                placeholder="25000"
                                min="0"
                                required
                            >

                            <span>BDT / month</span>

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="availability">
                            Availability
                        </label>

                        <select
                            id="availability"
                            name="availability"
                            required
                        >

                            <option value="Available">
                                Available
                            </option>

                            <option value="Rented">
                                Rented
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="unitNumber">
                            Unit / House Number
                        </label>

                        <input
                            type="text"
                            id="unitNumber"
                            name="unit_number"
                            placeholder="e.g. 7/B"
                            required
                        >

                    </div>

                </div>

            </section>


            <section class="form-card">

                <div class="form-card-header">

                    <h2>
                        Location
                    </h2>

                    <p>
                        Provide the location of your property.
                    </p>

                </div>


                <div class="form-grid">

                    <div class="form-group">

                        <label for="area">
                            Area
                        </label>

                        <input
                            type="text"
                            id="area"
                            name="area"
                            placeholder="e.g. Banani"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="city">
                            City
                        </label>

                        <input
                            type="text"
                            id="city"
                            name="city"
                            placeholder="e.g. Dhaka"
                            required
                        >

                    </div>


                    <div class="form-group full">

                        <label for="address">
                            Full Address
                        </label>

                        <input
                            type="text"
                            id="address"
                            name="address"
                            placeholder="Enter full property address"
                        >

                    </div>

                </div>

            </section>


            <section class="form-card">

                <div class="form-card-header">

                    <h2>
                        Property Details
                    </h2>

                    <p>
                        Enter the size and room information.
                    </p>

                </div>


                <div class="form-grid">

                    <div class="form-group">

                        <label for="bedrooms">
                            Bedrooms
                        </label>

                        <input
                            type="number"
                            id="bedrooms"
                            name="bedrooms"
                            min="0"
                            placeholder="3"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="bathrooms">
                            Bathrooms
                        </label>

                        <input
                            type="number"
                            id="bathrooms"
                            name="bathrooms"
                            min="0"
                            placeholder="3"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="squareFeet">
                            Square Feet
                        </label>

                        <input
                            type="number"
                            id="squareFeet"
                            name="square_feet"
                            min="0"
                            placeholder="1200"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="floor">
                            Floor
                        </label>

                        <input
                            type="text"
                            id="floor"
                            name="floor"
                            placeholder="e.g. 7th Floor"
                        >

                    </div>

                </div>

            </section>


            <section class="form-card">

                <div class="form-card-header">

                    <h2>
                        Description
                    </h2>

                    <p>
                        Give potential tenants more information about the property.
                    </p>

                </div>


                <div class="form-group full">

                    <label for="description">
                        Property Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        placeholder="Describe the property, facilities, nearby locations, parking, security, etc."
                    ></textarea>

                </div>

            </section>


            <section class="form-card">

                <div class="form-card-header">

                    <h2>
                        Amenities
                    </h2>

                    <p>
                        Select the facilities available at this property.
                    </p>

                </div>


                <div class="amenities-grid">

                    <label class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="Parking">
                        <span>Parking</span>
                    </label>

                    <label class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="Lift">
                        <span>Lift</span>
                    </label>

                    <label class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="Security">
                        <span>Security</span>
                    </label>

                    <label class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="Generator">
                        <span>Generator</span>
                    </label>

                    <label class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="Gas">
                        <span>Gas</span>
                    </label>

                    <label class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="Water">
                        <span>Water</span>
                    </label>

                    <label class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="Internet">
                        <span>Internet</span>
                    </label>

                    <label class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="CCTV">
                        <span>CCTV</span>
                    </label>

                </div>

            </section>


            <section class="form-card">

                <div class="form-card-header">

                    <h2>
                        Property Photos
                    </h2>

                    <p>
                        Upload one or more photos of your property.
                    </p>

                </div>


                <div class="photo-upload">

                    <input
                        type="file"
                        id="propertyPhotos"
                        name="property_photos[]"
                        accept="image/png,image/jpeg,image/jpg"
                        multiple
                    >

                    <div class="upload-content">

                        <div class="upload-icon">
                            ↑
                        </div>

                        <strong>
                            Click to upload photos
                        </strong>

                        <span>
                            PNG, JPG or JPEG
                        </span>

                    </div>

                </div>


                <div
                    class="photo-preview-grid"
                    id="photoPreview"
                ></div>

            </section>


            <div class="form-actions">

                <a
                    href="properties.php"
                    class="secondary-btn"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="primary-btn"
                >
                    Save Property
                </button>

            </div>

        </form>

    </main>

</div>


<script src="../js/landlord.js"></script>


<script>

const photoInput = document.getElementById("propertyPhotos");
const photoPreview = document.getElementById("photoPreview");
const addPropertyForm = document.getElementById("addPropertyForm");


photoInput.addEventListener("change", function () {

    photoPreview.innerHTML = "";

    const files = Array.from(this.files);

    files.forEach(function (file) {

        if (!file.type.startsWith("image/")) {
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {

            const wrapper = document.createElement("div");

            wrapper.className = "photo-preview";

            wrapper.innerHTML = `
                <img src="${event.target.result}" alt="Property Photo">
            `;

            photoPreview.appendChild(wrapper);

        };

        reader.readAsDataURL(file);

    });

});


addPropertyForm.addEventListener("submit", function (event) {

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