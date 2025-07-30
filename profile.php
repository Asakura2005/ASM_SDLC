<?php
session_start();
require_once 'config/database.php';
require_once 'functions/auth.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$page_title = 'Hồ sơ cá nhân';
// Khởi tạo kết nối PDO
$database = new Database();
$pdo = $database->getConnection();
// Lấy thông tin user từ database
$user = null;
$stmt_user = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt_user->execute([$_SESSION['user_id']]);
if ($stmt_user->rowCount() > 0) {
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);
} else {
    // fallback nếu không tìm thấy user
    $user = [
        'full_name' => '',
        'email' => '',
        'phone' => '',
        'created_at' => ''
    ];
}
$success = '';
$error = '';

// Get user's orders
$stmt = $pdo->prepare("
    SELECT o.*, 
           GROUP_CONCAT(CONCAT(mi.name, ' (', oi.quantity, ')') SEPARATOR ', ') as product_list
    FROM orders o 
    LEFT JOIN order_items oi ON o.order_id = oi.order_id 
    LEFT JOIN menu_items mi ON oi.item_id = mi.item_id
    WHERE o.user_id = ? 
    GROUP BY o.order_id 
    ORDER BY o.order_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
   
    
    if (empty($full_name)) {
        $error = 'Họ tên không được để trống.';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE user_id = ?");
        if ($stmt->execute([$full_name, $phone, $_SESSION['user_id']])) {
            $success = 'Cập nhật thông tin thành công.';
            $user['full_name'] = $full_name;
            $user['phone'] = $phone;
        } else {
            $error = 'Có lỗi xảy ra. Vui lòng thử lại.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="profile-avatar mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h5><?php echo htmlspecialchars($user['full_name']); ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                    <small class="text-muted">Thành viên từ <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></small>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#profile-info">Thông tin cá nhân</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#order-history">Lịch sử mua hàng</a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Profile Info Tab -->
                        <div class="tab-pane fade show active" id="profile-info">
                            <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Họ tên</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                    <small class="form-text text-muted">Email không thể thay đổi</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone']); ?>">
                                </div>
                                
                                
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Cập nhật thông tin
                                </button>
                            </form>
                        </div>
                        
                        <!-- Order History Tab -->
                        <div class="tab-pane fade" id="order-history">
                            <?php if (empty($orders)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <h5>Chưa có đơn hàng nào</h5>
                                <p class="text-muted">Bạn chưa thực hiện giao dịch nào.</p>
                                <a href="index.php" class="btn btn-primary">Xem thực đơn</a>
                            </div>
                            <?php else: ?>
                            <div class="order-history">
                                <?php foreach ($orders as $order): ?>
                                <div class="order-item mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="mb-1">Đơn hàng #<?php echo $order['order_id']; ?></h6>
                                                    <p class="mb-1 text-muted"><?php echo $order['product_list']; ?></p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?>
                                                    </small>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <div class="mb-2">
                                                        <span class="badge bg-<?php 
                                                            echo $order['status'] == 'confirmed' ? 'success' : 
                                                                 ($order['status'] == 'pending' ? 'warning' : 
                                                                  ($order['status'] == 'delivered' ? 'info' : 'danger')); 
                                                        ?>">
                                                            <?php 
                                                                $status_text = [
                                                                    'pending' => 'Chờ xử lý',
                                                                    'confirmed' => 'Đã xác nhận',
                                                                    'delivered' => 'Đã giao',
                                                                    'cancelled' => 'Đã hủy'
                                                                ];
                                                                echo $status_text[$order['status']];
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <div class="fw-bold"><?php echo number_format($order['total_price']); ?>đ</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>