<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '/xampp/htdocs/PHP/fooddelivery/config/database.php';
require_once '/xampp/htdocs/PHP/fooddelivery/functions/cart.php';


$database = new Database();
$db = $database->getConnection();

// Get cart count
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff6b35;
            --primary-dark: #e55a2b;
            --secondary-color: #ffa726;
            --bs-primary: #ff6b35;
            --bs-primary-rgb: 255, 107, 53;
        }
        .bg-primary { background-color: var(--primary-color) !important; }
        .btn-primary { 
            background-color: var(--primary-color); 
            border-color: var(--primary-color);
        }
        .btn-primary:hover { 
            background-color: var(--primary-dark); 
            border-color: var(--primary-dark);
        }
        .text-primary { color: var(--primary-color) !important; }
        .navbar-brand { font-weight: bold; }
        .card { transition: transform 0.2s; }
        .card:hover { transform: translateY(-2px); }
        .food-card img { height: 200px; object-fit: cover; }
        .cart-badge { 
            position: absolute; 
            top: -8px; 
            right: -8px; 
            background: var(--primary-color);
        }
        
        /* Ensure body takes full height for sticky footer */
        html, body {
            height: 100%;
        }
        
        body {
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1 0 auto;
        }
    </style>
</head>
<body class="bg-light d-flex flex-column">
    <?php include '/xampp/htdocs/PHP/fooddelivery/components/Header.php'; ?>
    <main>