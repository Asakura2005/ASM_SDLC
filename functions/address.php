<?php
/**
 * Address Management Functions
 * Handles delivery address operations, validation, and formatting
 */

/**
 * Get user's addresses
 */
function get_user_addresses($db, $user_id) {
    $query = "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, address_name ASC";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get user's default address
 */
function get_default_address($db, $user_id) {
    $query = "SELECT * FROM user_addresses WHERE user_id = ? AND is_default = TRUE LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Add new address for user
 */
function add_user_address($db, $user_id, $address_data) {
    try {
        $db->beginTransaction();
        
        // If this is set as default, unset other defaults
        if ($address_data['is_default']) {
            $update_query = "UPDATE user_addresses SET is_default = FALSE WHERE user_id = ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->execute([$user_id]);
        }
        
        $query = "INSERT INTO user_addresses (user_id, address_name, full_address, city, state, postal_code, country, is_default) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            $user_id,
            $address_data['address_name'],
            $address_data['full_address'],
            $address_data['city'],
            $address_data['state'],
            $address_data['postal_code'],
            $address_data['country'],
            $address_data['is_default'] ? 1 : 0
        ]);
        
        $db->commit();
        return ['success' => true, 'address_id' => $db->lastInsertId()];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'message' => 'Error adding address: ' . $e->getMessage()];
    }
}

/**
 * Update existing address
 */
function update_user_address($db, $address_id, $user_id, $address_data) {
    try {
        $db->beginTransaction();
        
        // If this is set as default, unset other defaults
        if ($address_data['is_default']) {
            $update_query = "UPDATE user_addresses SET is_default = FALSE WHERE user_id = ? AND address_id != ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->execute([$user_id, $address_id]);
        }
        
        $query = "UPDATE user_addresses SET 
                  address_name = ?, full_address = ?, city = ?, state = ?, 
                  postal_code = ?, country = ?, is_default = ?
                  WHERE address_id = ? AND user_id = ?";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            $address_data['address_name'],
            $address_data['full_address'],
            $address_data['city'],
            $address_data['state'],
            $address_data['postal_code'],
            $address_data['country'],
            $address_data['is_default'] ? 1 : 0,
            $address_id,
            $user_id
        ]);
        
        $db->commit();
        return ['success' => true];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'message' => 'Error updating address: ' . $e->getMessage()];
    }
}

/**
 * Delete user address
 */
function delete_user_address($db, $address_id, $user_id) {
    try {
        // Check if this is the default address
        $check_query = "SELECT is_default FROM user_addresses WHERE address_id = ? AND user_id = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$address_id, $user_id]);
        $address = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$address) {
            return ['success' => false, 'message' => 'Address not found'];
        }
        
        $db->beginTransaction();
        
        // Delete the address
        $delete_query = "DELETE FROM user_addresses WHERE address_id = ? AND user_id = ?";
        $delete_stmt = $db->prepare($delete_query);
        $delete_stmt->execute([$address_id, $user_id]);
        
        // If deleted address was default, set another as default
        if ($address['is_default']) {
            $set_default_query = "UPDATE user_addresses SET is_default = TRUE 
                                  WHERE user_id = ? ORDER BY created_at ASC LIMIT 1";
            $set_default_stmt = $db->prepare($set_default_query);
            $set_default_stmt->execute([$user_id]);
        }
        
        $db->commit();
        return ['success' => true];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'message' => 'Error deleting address: ' . $e->getMessage()];
    }
}

/**
 * Validate address data
 */
function validate_address($address_data) {
    $errors = [];
    
    if (empty($address_data['full_address'])) {
        $errors[] = 'Full address is required';
    }
    
    if (empty($address_data['city'])) {
        $errors[] = 'City is required';
    }
    
    if (empty($address_data['country'])) {
        $errors[] = 'Country is required';
    }
    
    if (!empty($address_data['postal_code']) && !preg_match('/^[0-9]{5,10}$/', $address_data['postal_code'])) {
        $errors[] = 'Invalid postal code format';
    }
    
    return $errors;
}

/**
 * Format address for display
 */
function format_address_display($address) {
    $parts = [];
    
    if (!empty($address['full_address'])) {
        $parts[] = $address['full_address'];
    }
    
    if (!empty($address['city'])) {
        $parts[] = $address['city'];
    }
    
    if (!empty($address['state'])) {
        $parts[] = $address['state'];
    }
    
    if (!empty($address['postal_code'])) {
        $parts[] = $address['postal_code'];
    }
    
    if (!empty($address['country'])) {
        $parts[] = $address['country'];
    }
    
    return implode(', ', $parts);
}

/**
 * Get Vietnam provinces/cities for dropdown
 */
function get_vietnam_provinces() {
    return [
        'An Giang', 'Bà Rịa - Vũng Tàu', 'Bắc Giang', 'Bắc Kạn', 'Bạc Liêu',
        'Bắc Ninh', 'Bến Tre', 'Bình Định', 'Bình Dương', 'Bình Phước',
        'Bình Thuận', 'Cà Mau', 'Cao Bằng', 'Đắk Lắk', 'Đắk Nông',
        'Điện Biên', 'Đồng Nai', 'Đồng Tháp', 'Gia Lai', 'Hà Giang',
        'Hà Nam', 'Hà Tĩnh', 'Hải Dương', 'Hậu Giang', 'Hòa Bình',
        'Hưng Yên', 'Khánh Hòa', 'Kiên Giang', 'Kon Tum', 'Lai Châu',
        'Lâm Đồng', 'Lạng Sơn', 'Lào Cai', 'Long An', 'Nam Định',
        'Nghệ An', 'Ninh Bình', 'Ninh Thuận', 'Phú Thọ', 'Quảng Bình',
        'Quảng Nam', 'Quảng Ngãi', 'Quảng Ninh', 'Quảng Trị', 'Sóc Trăng',
        'Sơn La', 'Tây Ninh', 'Thái Bình', 'Thái Nguyên', 'Thanh Hóa',
        'Thừa Thiên Huế', 'Tiền Giang', 'Trà Vinh', 'Tuyên Quang', 'Vĩnh Long',
        'Vĩnh Phúc', 'Yên Bái', 'Phú Yên', 'Cần Thơ', 'Đà Nẵng',
        'Hải Phòng', 'Hà Nội', 'Ho Chi Minh City'
    ];
}
?>