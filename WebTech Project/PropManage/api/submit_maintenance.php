<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$tenantId = $_SESSION['user_id'] ?? 3;
$title = trim($_POST['issue_type'] ?? 'General Issue');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? 'Plumbing');
$priority = trim($_POST['priority'] ?? 'Standard');

if (empty($title) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Issue type and description are required.']);
    exit();
}

if ($db_connected && $pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO maintenance_requests (tenant_id, property_id, landlord_id, title, description, priority, category, status)
                               VALUES (:tid, 1, 2, :title, :desc, :pri, :cat, 'pending')");
        $stmt->execute([
            ':tid'   => $tenantId,
            ':title' => $title,
            ':desc'  => $description,
            ':pri'   => $priority,
            ':cat'   => $category
        ]);
        $reqId = $pdo->lastInsertId();


        $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action_type, details, ip_address)
                                 VALUES (:uid, 'Maintenance Ticket', :details, :ip)");
        $logStmt->execute([
            ':uid'     => $tenantId,
            ':details' => "Maintenance ticket #$reqId submitted: $title",
            ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        echo json_encode(['success' => true, 'request_id' => $reqId, 'message' => 'Maintenance ticket saved in database!']);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['success' => true, 'message' => 'Maintenance ticket recorded in demo mode!']);
    exit();
}
?>
