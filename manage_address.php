<?php
session_start();
require_once '../fooddelivery/config/database.php';
require_once '../fooddelivery/functions/auth.php';
require_once '../fooddelivery/functions/address.php';

require_login();

$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $address_data = [
            'address_name' => trim($_POST['address_name']),
            'full_address' => trim($_POST['full_address']),
            'city' => trim($_POST['city']),
            'state' => trim($_POST['state']),
            'postal_code' => trim($_POST['postal_code']),
            'country' => trim($_POST['country']) ?: 'Vietnam',
            'is_default' => isset($_POST['is_default'])
        ];
        
        $validation_errors = validate_address($address_data);
        if (!empty($validation_errors)) {
            $_SESSION['error'] = implode(', ', $validation_errors);
        } else {
            $result = add_user_address($db, $_SESSION['user_id'], $address_data);
            if ($result['success']) {
                $_SESSION['success'] = 'Thêm địa chỉ thành công';
            } else {
                $_SESSION['error'] = $result['message'];
            }
        }
    }
    
    if ($action === 'update') {
        $address_id = (int)$_POST['address_id'];
        $address_data = [
            'address_name' => trim($_POST['address_name']),
            'full_address' => trim($_POST['full_address']),
            'city' => trim($_POST['city']),
            'state' => trim($_POST['state']),
            'postal_code' => trim($_POST['postal_code']),
            'country' => trim($_POST['country']) ?: 'Vietnam',
            'is_default' => isset($_POST['is_default'])
        ];
        
        $validation_errors = validate_address($address_data);
        if (!empty($validation_errors)) {
            $_SESSION['error'] = implode(', ', $validation_errors);
        } else {
            $result = update_user_address($db, $address_id, $_SESSION['user_id'], $address_data);
            if ($result['success']) {
                $_SESSION['success'] = 'Cập nhật địa chỉ thành công';
            } else {
                $_SESSION['error'] = $result['message'];
            }
        }
    }
    
    if ($action === 'delete') {
        $address_id = (int)$_POST['address_id'];
        $result = delete_user_address($db, $address_id, $_SESSION['user_id']);
        if ($result['success']) {
            $_SESSION['success'] = 'Xóa địa chỉ thành công';
        } else {
            $_SESSION['error'] = $result['message'];
        }
    }
}

// Redirect back to checkout or profile
$redirect = $_POST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? 'checkout.php';
header('Location: ' . $redirect);
exit();
?>