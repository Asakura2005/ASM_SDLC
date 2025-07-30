<?php
$page_title = "Quản lý mã giảm giá";
include '../includes/header.php';
include '../functions/auth.php';

require_admin();

$error = '';
$success = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_discount'])) {
        $code = strtoupper(trim($_POST['code']));
        $description = trim($_POST['description']);
        $discount_type = $_POST['discount_type'];
        $discount_value = (float)$_POST['discount_value'];
        $min_order = (float)$_POST['min_order'];
        $max_discount = $_POST['max_discount'] ? (float)$_POST['max_discount'] : null;
        $start_date = $_POST['start_date'] ?: null;
        $end_date = $_POST['end_date'] ?: null;
        
        if (empty($code) || $discount_value <= 0) {
            $error = 'Vui lòng điền đầy đủ thông tin';
        } else {
            // Check if code exists
            $check_query = "SELECT discount_id FROM discounts WHERE code = ?";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->execute([$code]);
            
            if ($check_stmt->rowCount() > 0) {
                $error = 'Mã giảm giá đã tồn tại';
            } else {
                $query = "INSERT INTO discounts (code, description, discount_type, discount_value, min_order, max_discount, start_date, end_date) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                if ($stmt->execute([$code, $description, $discount_type, $discount_value, $min_order, $max_discount, $start_date, $end_date])) {
                    $success = 'Thêm mã giảm giá thành công';
                } else {
                    $error = 'Có lỗi xảy ra khi thêm mã giảm giá';
                }
            }
        }
    }
    
    if (isset($_POST['toggle_status'])) {
        $discount_id = (int)$_POST['discount_id'];
        $query = "UPDATE discounts SET active = NOT active WHERE discount_id = ?";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$discount_id])) {
            $success = 'Cập nhật trạng thái thành công';
        } else {
            $error = 'Có lỗi xảy ra khi cập nhật trạng thái';
        }
    }
    
    if (isset($_POST['delete_discount'])) {
        $discount_id = (int)$_POST['discount_id'];
        $query = "DELETE FROM discounts WHERE discount_id = ?";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$discount_id])) {
            $success = 'Xóa mã giảm giá thành công';
        } else {
            $error = 'Có lỗi xảy ra khi xóa mã giảm giá';
        }
    }
}

// Get discounts
$query = "SELECT * FROM discounts ORDER BY discount_id DESC";
$stmt = $db->prepare($query);
$stmt->execute(); // Add this line
$discounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý mã giảm giá</h1>
        <div>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDiscountModal">
                <i class="fas fa-plus me-1"></i>Thêm mã giảm giá
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
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Mô tả</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Đơn tối thiểu</th>
                            <th>Thời hạn</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($discounts as $discount): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($discount['code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($discount['description']); ?></td>
                            <td>
                                <?php echo $discount['discount_type'] == 'percent' ? 'Phần trăm' : 'Số tiền'; ?>
                            </td>
                            <td>
                                <?php 
                                if ($discount['discount_type'] == 'percent') {
                                    echo '<span class="badge bg-info">' . $discount['discount_value'] . '%</span>';
                                    if ($discount['max_discount']) {
                                        echo '<br><small class="text-muted">Tối đa: ' . number_format($discount['max_discount']) . 'đ</small>';
                                    }
                                } else {
                                    echo '<span class="badge bg-success">' . number_format($discount['discount_value']) . 'đ</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($discount['min_order'] > 0): ?>
                                    <span class="text-primary"><?php echo number_format($discount['min_order']); ?>đ</span>
                                <?php else: ?>
                                    <span class="text-muted">Không yêu cầu</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($discount['start_date'] || $discount['end_date']): ?>
                                    <div class="d-flex flex-column">
                                        <small>
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            <?php echo $discount['start_date'] ? date('d/m/Y', strtotime($discount['start_date'])) : '∞'; ?>
                                        </small>
                                        <small>
                                            <i class="fas fa-calendar-times me-1"></i>
                                            <?php echo $discount['end_date'] ? date('d/m/Y', strtotime($discount['end_date'])) : '∞'; ?>
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <span class="text-success">
                                        <i class="fas fa-infinity me-1"></i>Không giới hạn
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $discount['active'] ? 'success' : 'danger'; ?> d-flex align-items-center">
                                    <i class="fas fa-<?php echo $discount['active'] ? 'check-circle' : 'times-circle'; ?> me-1"></i>
                                    <?php echo $discount['active'] ? 'Hoạt động' : 'Tạm dừng'; ?>
                                </span>
                                <?php
                                // Check if discount is expired
                                $now = new DateTime();
                                $end_date = $discount['end_date'] ? new DateTime($discount['end_date']) : null;
                                if ($end_date && $end_date < $now): ?>
                                    <br><small class="text-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Đã hết hạn
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="discount_id" value="<?php echo $discount['discount_id']; ?>">
                                        <button type="submit" name="toggle_status" 
                                                class="btn btn-sm btn-outline-<?php echo $discount['active'] ? 'warning' : 'success'; ?>"
                                                title="<?php echo $discount['active'] ? 'Tạm dừng' : 'Kích hoạt'; ?>">
                                            <i class="fas fa-<?php echo $discount['active'] ? 'pause' : 'play'; ?>"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="copyDiscountCode('<?php echo $discount['code']; ?>')"
                                            title="Sao chép mã">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này?')">
                                        <input type="hidden" name="discount_id" value="<?php echo $discount['discount_id']; ?>">
                                        <button type="submit" name="delete_discount" class="btn btn-sm btn-outline-danger"
                                                title="Xóa mã giảm giá">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Discount Modal -->
<div class="modal fade" id="addDiscountModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm mã giảm giá mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Mã giảm giá *</label>
                                <input type="text" class="form-control" name="code" required 
                                       style="text-transform: uppercase;" placeholder="VD: GIAM10">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Loại giảm giá *</label>
                                <select class="form-select" name="discount_type" required onchange="toggleDiscountType(this)">
                                    <option value="percent">Phần trăm (%)</option>
                                    <option value="fixed">Số tiền cố định (đ)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="2" 
                                  placeholder="Mô tả về mã giảm giá"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Giá trị giảm *</label>
                                <input type="number" class="form-control" name="discount_value" 
                                       min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Đơn hàng tối thiểu</label>
                                <input type="number" class="form-control" name="min_order" 
                                       min="0" step="1000" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="maxDiscountField">
                        <label class="form-label">Giảm tối đa (chỉ áp dụng cho phần trăm)</label>
                        <input type="number" class="form-control" name="max_discount" 
                               min="0" step="1000" placeholder="Để trống nếu không giới hạn">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày bắt đầu</label>
                                <input type="date" class="form-control" name="start_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày kết thúc</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="add_discount" class="btn btn-primary">Thêm mã giảm giá</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDiscountType(select) {
    const maxDiscountField = document.getElementById('maxDiscountField');
    if (select.value === 'percent') {
        maxDiscountField.style.display = 'block';
    } else {
        maxDiscountField.style.display = 'none';
    }
}

// Auto uppercase discount code
document.querySelector('input[name="code"]').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Copy discount code to clipboard
function copyDiscountCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check me-2"></i>Đã sao chép mã: ${code}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toast);
        });
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Không thể sao chép mã. Vui lòng sao chép thủ công: ' + code);
    });
}
</script>

<?php include '../includes/footer.php'; ?>