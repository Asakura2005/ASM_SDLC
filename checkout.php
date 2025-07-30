<?php
ob_start();
$page_title = "Thanh toán";
include '../fooddelivery/includes/header.php';
include '../fooddelivery/functions/auth.php';
include_once '../fooddelivery/functions/cart.php';
include '../fooddelivery/functions/orders.php';
include '../fooddelivery/functions/address.php';

require_login();

$cart_items = get_cart_items($db);
$total = get_cart_total($db);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

$error = '';
$success = '';
$discount_amount = 0;
$discount_code = '';
$discount_info = null;

// Get user addresses
$user_addresses = get_user_addresses($db, $_SESSION['user_id']);
$default_address = get_default_address($db, $_SESSION['user_id']);

// Handle discount code
if (isset($_POST['apply_discount'])) {
    $discount_code = trim($_POST['discount_code']);
    if ($discount_code) {
        $discount = get_discount($db, $discount_code);
        if ($discount) {
            if ($total >= $discount['min_order']) {
                $discount_amount = calculate_discount_amount($discount, $total);
                $discount_info = $discount;
                $success = 'Áp dụng mã giảm giá thành công';
            } else {
                $error = 'Đơn hàng tối thiểu ' . number_format($discount['min_order']) . 'đ để sử dụng mã này';
            }
        } else {
            $error = 'Mã giảm giá không hợp lệ hoặc đã hết hạn';
        }
    }
}

// Handle order placement
if (isset($_POST['place_order'])) {
    $discount_code = $_POST['discount_code'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $selected_address_id = $_POST['address_id'] ?? '';
    $delivery_notes = trim($_POST['delivery_notes'] ?? '');
    
    if (empty($payment_method) || empty($selected_address_id)) {
        $error = 'Vui lòng chọn địa chỉ giao hàng và phương thức thanh toán';
    } else {
        // Get selected address
        $address_query = "SELECT * FROM user_addresses WHERE address_id = ? AND user_id = ?";
        $address_stmt = $db->prepare($address_query);
        $address_stmt->execute([$selected_address_id, $_SESSION['user_id']]);
        $selected_address = $address_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$selected_address) {
            $error = 'Địa chỉ giao hàng không hợp lệ';
        } else {
            $delivery_address = [
                'full_address' => $selected_address['full_address'],
                'city' => $selected_address['city'],
                'state' => $selected_address['state'],
                'postal_code' => $selected_address['postal_code'],
                'country' => $selected_address['country'],
                'notes' => $delivery_notes
            ];
            
            $result = create_order($db, $_SESSION['user_id'], $discount_code, $delivery_address);
        }
        
        if ($result['success']) {
            // Create payment record
            $final_total = $total - $discount_amount;
            $payment_query = "INSERT INTO payments (order_id, payment_method, amount, status) VALUES (?, ?, ?, 'paid')";
            $payment_stmt = $db->prepare($payment_query);
            $payment_stmt->execute([$result['order_id'], $payment_method, $final_total]);
            
            $_SESSION['success'] = 'Đặt hàng thành công! Mã đơn hàng: #' . $result['order_id'];
            header('Location: orders.php');
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

$final_total = $total - $discount_amount;
?>

<div class="container my-4">
    <h1 class="mb-4">Thanh toán</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Delivery Address Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Địa chỉ giao hàng</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addressModal">
                        <i class="fas fa-plus me-1"></i>Thêm địa chỉ
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($user_addresses)): ?>
                        <div class="text-center py-3">
                            <i class="fas fa-map-marker-alt fa-2x text-muted mb-2"></i>
                            <p class="text-muted">Chưa có địa chỉ giao hàng</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addressModal">
                                Thêm địa chỉ đầu tiên
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="address-selection">
                            <?php
                            // Re-fetch user addresses after adding a new one
                            $user_addresses = get_user_addresses($db, $_SESSION['user_id']);
                            foreach ($user_addresses as $address): ?>
                            <div class="form-check address-option mb-3">
                                <input class="form-check-input" type="radio" name="address_id" 
                                       id="address_<?php echo $address['address_id']; ?>" 
                                       value="<?php echo $address['address_id']; ?>"
                                       <?php echo $address['is_default'] ? 'checked' : ''; ?>>
                                <label class="form-check-label w-100" for="address_<?php echo $address['address_id']; ?>">
                                    <div class="address-card p-3 border rounded">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <?php echo htmlspecialchars($address['address_name']); ?>
                                                    <?php if ($address['is_default']): ?>
                                                        <span class="badge bg-primary ms-2">Mặc định</span>
                                                    <?php endif; ?>
                                                </h6>
                                                <p class="mb-0 text-muted">
                                                    <?php echo htmlspecialchars(format_address_display($address)); ?>
                                                </p>
                                            </div>
                                            <a href="edit_address.php?address_id=<?php echo $address['address_id']; ?>" 
                                               class="btn btn-sm btn-outline-secondary me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="delete_address.php" style="display:inline;" 
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này?');">
                                                <input type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                 
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Chi tiết đơn hàng</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                            <small class="text-muted">Số lượng: <?php echo $item['quantity']; ?></small>
                        </div>
                        <span><?php echo number_format($item['subtotal']); ?>đ</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Discount Code -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Mã giảm giá</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="input-group">
                            <input type="text" class="form-control" name="discount_code" 
                                   placeholder="Nhập mã giảm giá" value="<?php echo htmlspecialchars($discount_code); ?>">
                            <button type="submit" name="apply_discount" class="btn btn-outline-primary">Áp dụng</button>
                        </div>
                        <?php if ($discount_info && $discount_amount > 0): ?>
                            <div class="mt-2 p-2 bg-success bg-opacity-10 border border-success rounded">
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Mã <strong><?php echo htmlspecialchars($discount_code); ?></strong> đã được áp dụng
                                    (<?php echo format_discount_display($discount_info, $discount_amount); ?>)
                                </small>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tóm tắt thanh toán</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <span><?php echo number_format($total); ?>đ</span>
                    </div>
                    <?php if ($discount_amount > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Giảm giá:</span>
                        <span>-<?php echo number_format($discount_amount); ?>đ</span>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí giao hàng:</span>
                        <span class="text-success">Miễn phí</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Tổng cộng:</strong>
                        <strong class="text-primary"><?php echo number_format($final_total); ?>đ</strong>
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="discount_code" value="<?php echo htmlspecialchars($discount_code); ?>">
                        <input type="hidden" name="address_id" id="selected_address_id" value="">
                        
                        <h6 class="mb-3">Phương thức thanh toán</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       id="cash" value="cash" required>
                                <label class="form-check-label" for="cash">
                                    <i class="fas fa-money-bill-wave me-2"></i>Tiền mặt
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       id="credit_card" value="credit_card" required>
                                <label class="form-check-label" for="credit_card">
                                    <i class="fas fa-credit-card me-2"></i>Thẻ tín dụng
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       id="e_wallet" value="e_wallet" required>
                                <label class="form-check-label" for="e_wallet">
                                    <i class="fas fa-mobile-alt me-2"></i>Ví điện tử
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" name="place_order" class="btn btn-primary w-100">
                            <i class="fas fa-check me-1"></i>Đặt hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm địa chỉ giao hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addressForm" action="manage_address.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên địa chỉ *</label>
                                <input type="text" class="form-control" name="address_name" 
                                       placeholder="Ví dụ: Nhà, Văn phòng" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Thành phố *</label>
                                <select class="form-select" name="city" required>
                                    <option value="">Chọn thành phố</option>
                                    <?php foreach (get_vietnam_provinces() as $province): ?>
                                        <option value="<?php echo $province; ?>"><?php echo $province; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ chi tiết *</label>
                        <textarea class="form-control" name="full_address" rows="2" 
                                  placeholder="Số nhà, tên đường, phường/xã, quận/huyện" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tỉnh/Thành phố</label>
                                <input type="text" class="form-control" name="state" 
                                       placeholder="Ví dụ: Ho Chi Minh">
                            </div>
                        </div>                   
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu địa chỉ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.address-option .address-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.address-option input[type="radio"]:checked + label .address-card {
    border-color: var(--bs-primary) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

.address-option:hover .address-card {
    border-color: var(--bs-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<script>
// Update hidden fields when address or notes change
document.addEventListener('DOMContentLoaded', function() {
    const addressRadios = document.querySelectorAll('input[name="address_id"]');
    const deliveryNotes = document.getElementById('delivery_notes');
    const checkoutForm = document.getElementById('checkoutForm');
    
    function updateHiddenFields() {
        const selectedAddress = document.querySelector('input[name="address_id"]:checked');
        if (selectedAddress) {
            document.getElementById('selected_address_id').value = selectedAddress.value;
        }
        
        if (deliveryNotes) {
            document.getElementById('selected_delivery_notes').value = deliveryNotes.value;
        }
    }
    
    addressRadios.forEach(radio => {
        radio.addEventListener('change', updateHiddenFields);
    });
    
    if (deliveryNotes) {
        deliveryNotes.addEventListener('input', updateHiddenFields);
    }
    
    // Initialize
    updateHiddenFields();
    
    // Form validation
    checkoutForm.addEventListener('submit', function(e) {
        const selectedAddress = document.querySelector('input[name="address_id"]:checked');
        const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
        
        if (!selectedAddress) {
            e.preventDefault();
            alert('Vui lòng chọn địa chỉ giao hàng');
            return false;
        }
        
        if (!selectedPayment) {
            e.preventDefault();
            alert('Vui lòng chọn phương thức thanh toán');
            return false;
        }
    });
});

function editAddress(addressId) {
    // Implement your edit address logic here
    // For example, you can redirect to an edit address page or open a modal
    alert('Edit address with ID: ' + addressId);
}
</script>
<?php include 'includes/footer.php'; ?>