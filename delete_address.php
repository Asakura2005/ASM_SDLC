<?php
include __DIR__ . '/config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Khởi tạo kết nối PDO
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address_id'])) {
    $address_id = $_POST['address_id'];
    $user_id = $_SESSION['user_id'];

    if ($db) {
        $stmt = $db->prepare("DELETE FROM user_addresses WHERE address_id = ? AND user_id = ?");
        $stmt->execute([$address_id, $user_id]);
    }
}

header('Location: checkout.php');
exit(); 