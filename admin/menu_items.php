<?php
$page_title = "Quản lý món ăn";
include '../includes/header.php';
include '../functions/auth.php';

require_admin();

$error = '';
$success = '';

// Handle form submissions (giữ nguyên code xử lý form)
if ($_POST) {
    // ... code xử lý form giữ nguyên ...
}

// Get menu items (giữ nguyên code query)
$query = "SELECT mi.*, c.name as category_name, r.name as restaurant_name 
          FROM menu_items mi 
          LEFT JOIN categories c ON mi.category_id = c.category_id 
          LEFT JOIN restaurants r ON mi.restaurant_id = r.restaurant_id 
          ORDER BY mi.name";
$stmt = $db->prepare($query);
$stmt->execute();
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories and restaurants for forms (giữ nguyên)
$cat_query = "SELECT * FROM categories ORDER BY name";
$cat_stmt = $db->prepare($cat_query);
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

$rest_query = "SELECT * FROM restaurants ORDER BY name";
$rest_stmt = $db->prepare($rest_query);
$rest_stmt->execute();
$restaurants = $rest_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý món ăn</h1>
        <div>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fas fa-plus me-1"></i>Thêm món ăn
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
                            <th>Hình ảnh</th>
                            <th>Tên món</th>
                            <th>Mô tả</th>
                            <th>Giá</th>
                            <th>Danh mục</th>
                            <th>Nhà hàng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menu_items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($item['description'], 0, 50)) . '...'; ?></td>
                            <td><?php echo number_format($item['price']); ?>đ</td>
                            <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['restaurant_name']); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" data-bs-target="#editItemModal<?php echo $item['item_id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa món ăn này?')">
                                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                    <button type="submit" name="delete_item" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ĐÚNG: Tất cả Edit Modals được đặt BÊN NGOÀI table -->
<?php foreach ($menu_items as $item): ?>
<div class="modal fade" id="editItemModal<?php echo $item['item_id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa món ăn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên món *</label>
                                <input type="text" class="form-control" name="name" 
                                       value="<?php echo htmlspecialchars($item['name']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Giá *</label>
                                <input type="number" class="form-control" name="price" 
                                       value="<?php echo $item['price']; ?>" min="0" step="1000" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($item['description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL hình ảnh</label>
                        <input type="url" class="form-control" name="image_url" 
                               value="<?php echo htmlspecialchars($item['image_url']); ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Danh mục</label>
                                <select class="form-select" name="category_id">
                                    <option value="">Chọn danh mục</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['category_id']; ?>" 
                                                <?php echo $item['category_id'] == $cat['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nhà hàng</label>
                                <select class="form-select" name="restaurant_id">
                                    <option value="">Chọn nhà hàng</option>
                                    <?php foreach ($restaurants as $rest): ?>
                                        <option value="<?php echo $rest['restaurant_id']; ?>" 
                                                <?php echo $item['restaurant_id'] == $rest['restaurant_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($rest['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="update_item" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm món ăn mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên món *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Giá *</label>
                                <input type="number" class="form-control" name="price" min="0" step="1000" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL hình ảnh</label>
                        <input type="url" class="form-control" name="image_url">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Danh mục</label>
                                <select class="form-select" name="category_id">
                                    <option value="">Chọn danh mục</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['category_id']; ?>">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nhà hàng</label>
                                <select class="form-select" name="restaurant_id">
                                    <option value="">Chọn nhà hàng</option>
                                    <?php foreach ($restaurants as $rest): ?>
                                        <option value="<?php echo $rest['restaurant_id']; ?>">
                                            <?php echo htmlspecialchars($rest['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="add_item" class="btn btn-primary">Thêm món ăn</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
