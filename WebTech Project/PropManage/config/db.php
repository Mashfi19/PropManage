<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'propmanage_db');
define('DB_USER', 'root');
define('DB_PASS', '');

$pdo = null;
$db_connected = false;
$db_error_message = '';


try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 3,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $db_connected = true;
} catch (PDOException $e) {

    try {
        $rootDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
        $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $db_connected = true;
    } catch (PDOException $e2) {
        $db_connected = false;
        $db_error_message = $e2->getMessage();
    }
}


if ($db_connected && $pdo) {
    try {
        $check = $pdo->query("SHOW TABLES LIKE 'users'")->fetch();
        if (!$check) {
            $schemaFile = __DIR__ . '/../database/schema.sql';
            if (file_exists($schemaFile)) {
                $sql = file_get_contents($schemaFile);
                $pdo->exec($sql);
            }
        }
    } catch (Exception $ex) {

    }
}


function getDb() {
    global $pdo;
    return $pdo;
}


function isUserLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}


function getActiveUserAvatar($default = '../images/default-avatar.svg') {
    global $pdo, $db_connected;
    if (!empty($_SESSION['avatar'])) {
        return $_SESSION['avatar'];
    }
    if (isUserLoggedIn() && $db_connected && $pdo) {
        try {
            $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE user_id = :uid LIMIT 1");
            $stmt->execute([':uid' => $_SESSION['user_id']]);
            $res = $stmt->fetchColumn();
            if (!empty($res)) {
                $_SESSION['avatar'] = $res;
                return $res;
            }
        } catch (Exception $e) {}
    }
    return $default;
}


function getCurrentUser() {
    if (!isUserLoggedIn()) return null;
    return [
        'id'        => $_SESSION['user_id'],
        'email'     => $_SESSION['user_email'] ?? '',
        'name'      => $_SESSION['user_name'] ?? 'User',
        'role'      => $_SESSION['role'] ?? 'tenant',
        'avatar'    => getActiveUserAvatar()
    ];
}


function requireRole($role) {
    if (!isUserLoggedIn()) {
        header("Location: ../auth/login.php");
        exit();
    }
    if ($_SESSION['role'] !== $role && $_SESSION['role'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }
}
?>
