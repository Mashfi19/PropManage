<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$tenantId = $_SESSION['user_id'] ?? 3;
$amount = floatval($_POST['amount'] ?? 22000.00);
$method = trim($_POST['method'] ?? 'bkash');
$type = trim($_POST['type'] ?? 'Monthly Rent');

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO payments (booking_id, tenant_id, amount, payment_type, payment_method, payment_status)
                               VALUES (1, :tid, :amount, :type, :method, 'completed')");
        $stmt->execute([
            ':tid'    => $tenantId,
            ':amount' => $amount,
            ':type'   => $type,
            ':method' => $method
        ]);
        $paymentId = $pdo->lastInsertId();


        $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action_type, details, ip_address)
                                 VALUES (:uid, 'Rent Payment', :details, :ip)");
        $logStmt->execute([
            ':uid'     => $tenantId,
            ':details' => "Payment of $amount BDT completed via $method (Payment #$paymentId)",
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        echo json_encode(['success' => true, 'payment_id' => $paymentId, 'message' => 'Payment recorded in database!']);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['success' => true, 'message' => 'Payment processed in demo mode!']);
    exit();
}
?>
