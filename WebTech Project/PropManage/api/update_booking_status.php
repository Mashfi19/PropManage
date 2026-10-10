<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$bookingId = intval($_POST['booking_id'] ?? 0);
$status = trim(strtolower($_POST['status'] ?? ''));

if (!$bookingId || !in_array($status, ['approved', 'rejected', 'pending'])) {
    echo json_encode(['success' => false, 'message' => 'Valid booking ID and status are required.']);
    exit();
}

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE booking_id = :id");
        $stmt->execute([':status' => $status, ':id' => $bookingId]);


        $userId = $_SESSION['user_id'] ?? 2;
        $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action_type, details, ip_address)
                                 VALUES (:uid, 'Booking Status Update', :details, :ip)");
        $logStmt->execute([
            ':uid'     => $userId,
            ':details' => "Booking #$bookingId marked as $status",
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        echo json_encode(['success' => true, 'message' => "Application status updated to " . strtoupper($status) . " in database!"]);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['success' => true, 'message' => "Application status updated to " . strtoupper($status) . " (Demo mode)."]);
    exit();
}
?>
