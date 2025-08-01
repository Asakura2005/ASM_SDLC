<?php
$page_title = "Store Management";
include '../includes/header.php';
include '../functions/auth.php';

require_admin();

$error = '';
$success = '';

// PDO connection from config
require_once '../config/database.php';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_store'])) {
        $name = trim($_POST['name']);
        $address = trim($_POST['address']);
        $phone = trim($_POST['phone']);
        $owner_id = !empty($_POST['owner_id']) ? (int)$_POST['owner_id'] : null;
        
        if (empty($name) || empty($address) || empty($phone)) {
            $error = 'Please fill in all required fields.';
        } else {
            $query = "INSERT INTO restaurants (name, address, phone, owner_id) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            if ($stmt->execute([$name, $address, $phone, $owner_id])) {
                $success = 'Store added successfully.';
            } else {
                $error = 'An error occurred while adding the store.';
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
            $error = 'Please fill in all required fields.';
        } else {
            $query = "UPDATE restaurants SET name = ?, address = ?, phone = ?, owner_id = ? WHERE restaurant_id = ?";
            $stmt = $db->prepare($query);
            if ($stmt->execute([$name, $address, $phone, $owner_id, $restaurant_id])) {
                $success = 'Store updated successfully.';
            } else {
                $error = 'An error occurred while updating the store.';
            }
        }
    }
    
    if (isset($_POST['delete_store'])) {
        $restaurant_id = (int)$_POST['restaurant_id'];
        
        // Check if there are any menu items belonging to this store
        $check_query = "SELECT COUNT(*) FROM menu_items WHERE restaurant_id = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$restaurant_id]);
        $menu_count = $check_stmt->fetchColumn();
        
        if ($menu_count > 0) {
            $error = 'Cannot delete this store because it has associated menu items. Please delete all related menu items first.';
        } else {
            $query = "DELETE FROM restaurants WHERE restaurant_id = ?";
            $stmt = $db->prepare($query);
            if ($stmt->execute([$restaurant_id])) {
                $success = 'Store deleted successfully.';
            } else {
                $error = 'An error occurred while deleting the store.';
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
        <h1>Store Management</h1>
        <div>
            <a href="index.php" class="btn btn-secondary">Go Back</a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStoreModal">
                <i class="fas fa-plus me-1"></i>Add Store
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
                    <h5 class="text-muted">No stores found.</h5>
                    <p class="text-muted">Click the "Add Store" button to get started.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Store Name</th>
                                <th>Address</th>
                                <th>Phone Number</th>
                                <th>Owner</th>
                                <th>Actions</th>
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
                                        <span class="text-muted">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" data-bs-target="#editStoreModal<?php echo $restaurant['restaurant_id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this store? This action cannot be undone.')">
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

<!-- Edit Store Modals - Placed outside the table to prevent flickering -->
<?php foreach ($restaurants as $restaurant): ?>
<div class="modal fade" id="editStoreModal<?php echo $restaurant['restaurant_id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Store</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="restaurant_id" value="<?php echo $restaurant['restaurant_id']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Store Name *</label>
                                <input type="text" class="form-control" name="name" 
                                       value="<?php echo htmlspecialchars($restaurant['name']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" class="form-control" name="phone" 
                                       value="<?php echo htmlspecialchars($restaurant['phone']); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address *</label>
                        <textarea class="form-control" name="address" rows="2" required><?php echo htmlspecialchars($restaurant['address']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id">
                            <option value="">Select an owner</option>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_store" class="btn btn-primary">Update</button>
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
                <h5 class="modal-title">Add New Store</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Store Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" class="form-control" name="phone" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address *</label>
                        <textarea class="form-control" name="address" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id">
                            <option value="">Select an owner</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['user_id']; ?>">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_store" class="btn btn-primary">Add Store</button>
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