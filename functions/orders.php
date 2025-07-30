<?php
function create_order($db, $user_id, $discount_code = null, $delivery_address = null) {
    try {
        $db->beginTransaction();
        
        // Calculate total
        $total = get_cart_total($db);
        $discount_amount = 0;
        
        if ($discount_code) {
            $discount = get_discount($db, $discount_code);
            if ($discount && $total >= $discount['min_order']) {
                if ($discount['discount_type'] == 'percent') {
                    $discount_amount = ($total * $discount['discount_value']) / 100;
                    if ($discount['max_discount'] && $discount_amount > $discount['max_discount']) {
                        $discount_amount = $discount['max_discount'];
                    }
                } else {
                    $discount_amount = $discount['discount_value'];
                }
            }
        }
        
        $final_total = $total - $discount_amount;
        
        // Create order
        $query = "INSERT INTO orders (user_id, total_price, discount_code, delivery_address, delivery_city, delivery_state, delivery_postal_code, delivery_country, delivery_notes) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            $user_id, 
            $final_total, 
            $discount_code,
            $delivery_address['full_address'] ?? null,
            $delivery_address['city'] ?? null,
            $delivery_address['state'] ?? null,
            $delivery_address['postal_code'] ?? null,
            $delivery_address['country'] ?? null,
            $delivery_address['notes'] ?? null
        ]);
        $order_id = $db->lastInsertId();
        
        // Add order items
        $cart_items = get_cart_items($db);
        $query = "INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        
        foreach ($cart_items as $item) {
            $stmt->execute([$order_id, $item['item_id'], $item['quantity'], $item['price']]);
        }
        
        $db->commit();
        clear_cart();
        
        return ['success' => true, 'order_id' => $order_id, 'message' => 'Đặt hàng thành công'];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'message' => 'Lỗi khi đặt hàng: ' . $e->getMessage()];
    }
}

function get_user_orders($db, $user_id) {
    $query = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_order_items($db, $order_id) {
    $query = "SELECT oi.*, mi.name, mi.image_url 
              FROM order_items oi 
              JOIN menu_items mi ON oi.item_id = mi.item_id 
              WHERE oi.order_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_order_with_address($db, $order_id) {
    $query = "SELECT o.*, u.full_name, u.phone, u.email 
              FROM orders o 
              JOIN users u ON o.user_id = u.user_id 
              WHERE o.order_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$order_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function format_delivery_address($order) {
    $address_parts = [];
    
    if (!empty($order['delivery_address'])) {
        $address_parts[] = $order['delivery_address'];
    }
    
    if (!empty($order['delivery_city'])) {
        $address_parts[] = $order['delivery_city'];
    }
    
    if (!empty($order['delivery_state'])) {
        $address_parts[] = $order['delivery_state'];
    }
    
    if (!empty($order['delivery_postal_code'])) {
        $address_parts[] = $order['delivery_postal_code'];
    }
    
    if (!empty($order['delivery_country'])) {
        $address_parts[] = $order['delivery_country'];
    }
    
    return implode(', ', $address_parts);
}

function get_discount($db, $code) {
    $query = "SELECT * FROM discounts WHERE code = ? AND active = 1 
              AND (start_date IS NULL OR start_date <= CURDATE()) 
              AND (end_date IS NULL OR end_date >= CURDATE())";
    $stmt = $db->prepare($query);
    $stmt->execute([$code]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function calculate_discount_amount($discount, $total) {
    if (!$discount || $total < $discount['min_order']) {
        return 0;
    }
    
    if ($discount['discount_type'] == 'percent') {
        $discount_amount = ($total * $discount['discount_value']) / 100;
        if ($discount['max_discount'] && $discount_amount > $discount['max_discount']) {
            $discount_amount = $discount['max_discount'];
        }
        return $discount_amount;
    } else {
        return min($discount['discount_value'], $total);
    }
}

function format_discount_display($discount, $discount_amount) {
    if ($discount['discount_type'] == 'percent') {
        $display = $discount['discount_value'] . '%';
        if ($discount['max_discount']) {
            $display .= ' (max ' . number_format($discount['max_discount']) . 'đ)';
        }
        $display .= ' = -' . number_format($discount_amount) . 'đ';
        return $display;
    } else {
        return '-' . number_format($discount_amount) . 'đ';
    }
}
?>