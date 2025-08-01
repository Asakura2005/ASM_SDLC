<?php
$page_title = "Order Management";
include '../includes/header.php';
include '../functions/auth.php';
include '../functions/orders.php';

require_admin();

$error = '';
$success = '';

// Handle status update
if ($_POST && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    $allowed_statuses = ['pending', 'confirmed', 'delivered', 'cancelled'];
    if (in_array($status, $allowed_statuses)) {
        $query = "UPDATE orders SET status = ? WHERE order_id = ?";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$status, $order_id])) {
            $success = 'Order status updated successfully.';
        } else {
            $error = 'An error occurred while updating the status.';
        }
    }
}

// Get orders
$query = "SELECT o.*, u.full_name, u.phone, u.email,
          CONCAT_WS(', ', 
              NULLIF(o.delivery_address, ''),
              NULLIF(o.delivery_city, ''),
              NULLIF(o.delivery_state, ''),
              NULLIF(o.delivery_postal_code, ''),
              NULLIF(o.delivery_country, '')
          ) as formatted_address
          FROM orders o 
          JOIN users u ON o.user_id = u.user_id 
          ORDER BY o.order_date DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get specific order details if viewing
$view_order = null;
$order_items = [];
if (isset($_GET['view'])) {
    $order_id = (int)$_GET['view'];
    $query = "SELECT o.*, u.full_name, u.phone, u.email,
              CONCAT_WS(', ', 
                  NULLIF(o.delivery_address, ''),
                  NULLIF(o.delivery_city, ''),
                  NULLIF(o.delivery_state, ''),
                  NULLIF(o.delivery_postal_code, ''),
                  NULLIF(o.delivery_country, '')
              ) as formatted_address
              FROM orders o 
              JOIN users u ON o.user_id = u.user_id 
              WHERE o.order_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$order_id]);
    $view_order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($view_order) {
        $order_items = get_order_items($db, $order_id);
    }
}
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Order Management</h1>
        <a href="index.php" class="btn btn-secondary">Go Back</a>
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
    
    <?php if ($view_order): ?>
        <!-- Order Details View -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order Details #<?php echo $view_order['order_id']; ?></h5>
                    <a href="orders.php" class="btn btn-sm btn-secondary">Back to List</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($view_order['full_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($view_order['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($view_order['phone']); ?></p>
                        
                        <?php if (!empty($view_order['formatted_address'])): ?>
                        <h6 class="mt-3">Delivery Address</h6>
                        <div class="delivery-address p-3 bg-light rounded">
                            <p class="mb-1"><i class="fas fa-map-marker-alt me-2 text-primary"></i>
                               <?php echo htmlspecialchars($view_order['formatted_address']); ?></p>
                            <?php if (!empty($view_order['delivery_notes'])): ?>
                                <p class="mb-0 text-muted"><small>
                                    <i class="fas fa-sticky-note me-1"></i>
                                    Notes: <?php echo htmlspecialchars($view_order['delivery_notes']); ?>
                                </small></p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <p><strong>Order Date:</strong> <?php echo date('d/m/Y H:i', strtotime($view_order['order_date'])); ?></p>
                        <p><strong>Total Price:</strong> <?php echo number_format($view_order['total_price']); ?>đ</p>
                        <p><strong>Discount Code:</strong> 
                            <?php if ($view_order['discount_code']): ?>
                                <span class="badge bg-success"><?php echo htmlspecialchars($view_order['discount_code']); ?></span>
                            <?php else: ?>
                                <span class="text-muted">None</span>
                            <?php endif; ?>
                        </p>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="order_id" value="<?php echo $view_order['order_id']; ?>">
                            <div class="input-group" style="max-width: 200px;">
                                <select class="form-select form-select-sm" name="status">
                                    <option value="pending" <?php echo $view_order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $view_order['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="delivered" <?php echo $view_order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $view_order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <h6 class="mt-4">Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Menu Item</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($item['price']); ?>đ</td>
                                <td><?php echo number_format($item['price'] * $item['quantity']); ?>đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Orders List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Delivery Address</th>
                                <th>Order Date</th>
                                <th>Total Price</th>
                                <th>Discount Code</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($order['full_name']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($order['phone']); ?></small>
                                </td>
                                <td>
                                    <?php if (!empty($order['formatted_address'])): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars(substr($order['formatted_address'], 0, 50)) . '...'; ?>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">No address provided</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                <td><?php echo number_format($order['total_price']); ?>đ</td>
                                <td>
                                    <?php if ($order['discount_code']): ?>
                                        <span class="badge bg-success"><?php echo htmlspecialchars($order['discount_code']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $order['status'] == 'delivered' ? 'success' : 
                                            ($order['status'] == 'confirmed' ? 'primary' : 
                                            ($order['status'] == 'cancelled' ? 'danger' : 'warning')); 
                                    ?>">
                                        <?php 
                                        $status_text = [
                                            'pending' => 'Pending',
                                            'confirmed' => 'Confirmed',
                                            'delivered' => 'Delivered',
                                            'cancelled' => 'Cancelled'
                                        ];
                                        echo $status_text[$order['status']];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="orders.php?view=<?php echo $order['order_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">View Details</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>