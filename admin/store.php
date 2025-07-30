<?php
$page_title = "Quản lý Cửa Hàng";
include '../includes/header.php';
include '../functions/auth.php';

require_admin();

$error = '';
$success = '';

// Kết nối PDO từ config
require_once '../config/database.php';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_store'])) {
        $name = trim($_POST['name']);
        $address = trim($_POST['address']);
        $phone = trim($_POST['phone']);
        $owner_id = !empty($_POST['owner_id']) ? (int)$_POST['owner_id'] : null;
        
        if (empty($name) || empty($address) || empty($phone)) {
            $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
        } else {
            $query = "INSERT INTO restaurants (name, address, phone, owner_id) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            if ($stmt->execute([$name, $address, $phone, $owner_id])) {
                $success = 'Thêm cửa hàng thành công';
            } else {
                $error = 'Có lỗi xảy ra khi thêm cửa hàng';
            }
        }
    }
    
    if (isset($_POST['update_store'])) {
        $restaurant_id = (int)$_POST['restaurant_id'];
        $name = trim($_POST['name']);
        $address = trim($_POST['address']);
        $phone = trim($_POST['phone']);
        $owner_id = !empty($_POST['owner_id']) ? (int)$_POST['owner_id'] : null;
        
        if (empty($name) || empty($address) || empty($phone)) {
            $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
        } else {
            $query = "UPDATE restaurants SET name = ?, address = ?, phone = ?, owner_id = ? WHERE restaurant_id = ?";
            $stmt = $db->prepare($query);
            if ($stmt->execute([$name, $address, $phone, $owner_id, $restaurant_id])) {
                $success = 'Cập nhật cửa hàng thành công';
            } else {
                $error = 'Có lỗi xảy ra khi cập nhật cửa hàng';
            }
        }
    }
    
    if (isset($_POST['delete_store'])) {
        $restaurant_id = (int)$_POST['restaurant_id'];
        
        // Kiểm tra xem có món ăn nào thuộc cửa hàng này không
        $check_query = "SELECT COUNT(*) FROM menu_items WHERE restaurant_id = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$restaurant_id]);
        $menu_count = $check_stmt->fetchColumn();
        
        if ($menu_count > 0) {
            $error = 'Không thể xóa cửa hàng này vì còn có món ăn liên quan. Vui lòng xóa tất cả món ăn trước.';
        } else {
            $query = "DELETE FROM restaurants WHERE restaurant_id = ?";
            $stmt = $db->prepare($query);
            if ($stmt->execute([$restaurant_id])) {
                $success = 'Xóa cửa hàng thành công';
            } else {
                $error = 'Có lỗi xảy ra khi xóa cửa hàng';
            }
        }
    }
}

// Get all restaurants with owner information
$query = "SELECT r.restaurant_id, r.name, r.address, r.phone, r.owner_id, u.username as owner_name 
          FROM restaurants r 
          LEFT JOIN users u ON r.owner_id = u.user_id 
          ORDER BY r.name";
$stmt = $db->prepare($query);
$stmt->execute();
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all users for owner selection
$user_query = "SELECT user_id, username FROM users WHERE role IN ('admin', 'restaurant_owner') ORDER BY username";
$user_stmt = $db->prepare($user_query);
$user_stmt->execute();
$users = $user_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý Cửa Hàng</h1>
        <div>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStoreModal">
                <i class="fas fa-plus me-1"></i>Thêm cửa hàng
            </button>
        </div>
    </div>
    
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
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($restaurants)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-store fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có cửa hàng nào</h5>
                    <p class="text-muted">Nhấn nút "Thêm cửa hàng" để bắt đầu</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên cửa hàng</th>
                                <th>Địa chỉ</th>
                                <th>Số điện thoại</th>
                                <th>Chủ sở hữu</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($restaurants as $restaurant): ?>
                            <tr>
                                <td><?php echo $restaurant['restaurant_id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($restaurant['name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($restaurant['address']); ?></td>
                                <td>
                                    <a href="tel:<?php echo htmlspecialchars($restaurant['phone']); ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($restaurant['phone']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($restaurant['owner_name']): ?>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($restaurant['owner_name']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa có</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" data-bs-target="#editStoreModal<?php echo $restaurant['restaurant_id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa cửa hàng này? Tất cả món ăn liên quan cũng sẽ bị ảnh hưởng.')">
                                        <input type="hidden" name="restaurant_id" value="<?php echo $restaurant['restaurant_id']; ?>">
                                        <button type="submit" name="delete_store" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Store Modals - Đặt bên ngoài table để tránh flickering -->
<?php foreach ($restaurants as $restaurant): ?>
<div class="modal fade" id="editStoreModal<?php echo $restaurant['restaurant_id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa cửa hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="restaurant_id" value="<?php echo $restaurant['restaurant_id']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên cửa hàng *</label>
                                <input type="text" class="form-control" name="name" 
                                       value="<?php echo htmlspecialchars($restaurant['name']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại *</label>
                                <input type="text" class="form-control" name="phone" 
                                       value="<?php echo htmlspecialchars($restaurant['phone']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ *</label>
                        <textarea class="form-control" name="address" rows="2" required><?php echo htmlspecialchars($restaurant['address']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chủ sở hữu</label>
                        <select class="form-select" name="owner_id">
                            <option value="">Chọn chủ sở hữu</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>" 
                                        <?php echo $restaurant['owner_id'] == $user['user_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="update_store" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Add Store Modal -->
<div class="modal fade" id="addStoreModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm cửa hàng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên cửa hàng *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại *</label>
                                <input type="text" class="form-control" name="phone" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ *</label>
                        <textarea class="form-control" name="address" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chủ sở hữu</label>
                        <select class="form-select" name="owner_id">
                            <option value="">Chọn chủ sở hữu</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="add_store" class="btn btn-primary">Thêm cửa hàng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>

<?php include '../includes/footer.php'; ?>
