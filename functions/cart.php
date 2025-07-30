<?php
function add_to_cart($item_id, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id] += $quantity;
    } else {
        $_SESSION['cart'][$item_id] = $quantity;
    }
}

function remove_from_cart($item_id) {
    if (isset($_SESSION['cart'][$item_id])) {
        unset($_SESSION['cart'][$item_id]);
    }
}

function update_cart($item_id, $quantity) {
    if ($quantity <= 0) {
        remove_from_cart($item_id);
    } else {
        $_SESSION['cart'][$item_id] = $quantity;
    }
}

function get_cart_items($db) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return array();
    }
    
    $item_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($item_ids) - 1) . '?';
    
    $query = "SELECT * FROM menu_items WHERE item_id IN ($placeholders)";
    $stmt = $db->prepare($query);
    $stmt->execute($item_ids);
    
    $items = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['quantity'] = $_SESSION['cart'][$row['item_id']];
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $items[] = $row;
    }
    
    return $items;
}

function get_cart_total($db) {
    $items = get_cart_items($db);
    $total = 0;
    foreach ($items as $item) {
        $total += $item['subtotal'];
    }
    return $total;
}

function clear_cart() {
    unset($_SESSION['cart']);
}
?>