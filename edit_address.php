<?php
session_start();
require_once '../fooddelivery/config/database.php';
require_once '../fooddelivery/functions/auth.php';
require_once '../fooddelivery/functions/address.php';

require_login();

$database = new Database();
$db = $database->getConnection();

// Lấy address_id từ GET
$address_id = isset($_GET['address_id']) ? (int)$_GET['address_id'] : 0;
$user_id = $_SESSION['user_id'];

// Lấy thông tin địa chỉ
$address = null;
if ($address_id > 0) {
    $stmt = $db->prepare("SELECT * FROM user_addresses WHERE address_id = ? AND user_id = ?");
    $stmt->execute([$address_id, $user_id]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (!$address) {
    $_SESSION['error'] = 'Không tìm thấy địa chỉ.';
    header('Location: checkout.php');
    exit();
}

function is_checked($val) {
    return $val ? 'checked' : '';
}

// Hiển thị form sửa địa chỉ
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa địa chỉ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4">Sửa địa chỉ giao hàng</h2>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <form method="POST" action="manage_address.php">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">
        <input type="hidden" name="redirect" value="checkout.php">
        <div class="mb-3">
            <label class="form-label">Tên địa chỉ *</label>
            <input type="text" class="form-control" name="address_name" value="<?php echo htmlspecialchars($address['address_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Thành phố *</label>
            <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($address['city']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Địa chỉ chi tiết *</label>
            <textarea class="form-control" name="full_address" rows="2" required><?php echo htmlspecialchars($address['full_address']); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Tỉnh/Thành phố</label>
            <input type="text" class="form-control" name="state" value="<?php echo htmlspecialchars($address['state']); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="checkout.php" class="btn btn-secondary ms-2">Hủy</a>
    </form>
</div>
</body>
</html> 