<?php
session_start();
require_once '../fooddelivery/functions/auth.php';
require_once '../fooddelivery/functions/cart.php';

require_login();

if ($_POST && isset($_POST['item_id'])) {
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    if ($item_id > 0 && $quantity > 0) {
        add_to_cart($item_id, $quantity);
        $_SESSION['success'] = 'Added the dish to the cart successfully.';
    }
}

$redirect = $_POST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? 'menu.php';
header('Location: ' . $redirect);
exit();
?>