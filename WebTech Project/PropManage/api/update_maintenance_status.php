<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$requestId = intval($_POST['request_id'] ?? 0);
$status = trim(strtolower($_POST['status'] ?? ''));

if (!$requestId || !in_array($status, ['pending', 'progress', 'resolved', 'rejected'])) {
    echo json_encode(['success' => false, 'message' => 'Valid request ID and status are required.']);
    exit();
}

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->prepare("UPDATE maintenance_requests SET status = :status WHERE request_id = :id");
        $stmt->execute([':status' => $status, ':id' => $requestId]);


        $userId = $_SESSION['user_id'] ?? 2;
        $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action_type, details, ip_address)
                                 VALUES (:uid, 'Maintenance Status Update', :details, :ip)");
        $logStmt->execute([
            ':uid'     => $userId,
            ':details' => "Maintenance ticket #$requestId marked as $status",
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        echo json_encode(['success' => true, 'message' => "Maintenance ticket status updated to " . strtoupper($status) . " in database!"]);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['success' => true, 'message' => "Maintenance ticket status updated to " . strtoupper($status) . " (Demo mode)."]);
    exit();
}
?>
