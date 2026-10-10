<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$tenantId = $_SESSION['user_id'] ?? 3;
$propertyId = intval($_POST['property_id'] ?? 1);
$startDate = $_POST['start_date'] ?? date('Y-m-d', strtotime('+30 days'));
$durationMonths = intval($_POST['duration_months'] ?? 12);
$monthlyRent = floatval($_POST['monthly_rent'] ?? 25000);
$message = trim($_POST['message'] ?? '');

$endDate = date('Y-m-d', strtotime("$startDate +$durationMonths months"));

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO bookings (tenant_id, property_id, start_date, end_date, monthly_rent, status)
                               VALUES (:tid, :pid, :sdate, :edate, :rent, 'pending')");
        $stmt->execute([
            ':tid'   => $tenantId,
            ':pid'   => $propertyId,
            ':sdate' => $startDate,
            ':edate' => $endDate,
            ':rent'  => $monthlyRent
        ]);
        $bookingId = $pdo->lastInsertId();


        $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action_type, details, ip_address)
                                 VALUES (:uid, 'Rental Request', :details, :ip)");
        $logStmt->execute([
            ':uid'     => $tenantId,
            ':details' => "Rental request submitted for property #$propertyId (Booking #$bookingId)",
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        echo json_encode(['success' => true, 'booking_id' => $bookingId, 'message' => 'Rental request saved in database!']);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
} else {

    echo json_encode(['success' => true, 'message' => 'Rental request recorded in demo mode!']);
    exit();
}
?>
