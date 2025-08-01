<?php
$page_title = "Admin Dashboard";
include '../includes/header.php';
include '../functions/auth.php';

require_admin();

// Get statistics
$stats = [];

// Total orders
$query = "SELECT COUNT(*) as total FROM orders";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total revenue
$query = "SELECT SUM(total_price) as total FROM orders WHERE status != 'cancelled'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Total menu items
$query = "SELECT COUNT(*) as total FROM menu_items";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_items'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total users
$query = "SELECT COUNT(*) as total FROM users WHERE role = 'customer'";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Recent orders
$query = "SELECT o.*, u.full_name FROM orders o 
          JOIN users u ON o.user_id = u.user_id 
          ORDER BY o.order_date DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Admin Dashboard</h1>
        <div class="btn-group">
            <a href="menu_items.php" class="btn btn-primary">Manage Menu Items</a>
            <a href="orders.php" class="btn btn-primary">Manage Orders</a>
            <a href="discounts.php" class="btn btn-primary">Discount Codes</a>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo number_format($stats['total_orders']); ?></h4>
                            <p class="mb-0">Total Orders</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo number_format($stats['total_revenue']); ?>đ</h4>
                            <p class="mb-0">Total Revenue</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo number_format($stats['total_items']); ?></h4>
                            <p class="mb-0">Menu Items</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-utensils fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo number_format($stats['total_users']); ?></h4>
                            <p class="mb-0">Customers</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Orders</h5>
        </div>
        <div class="card-body">
            <?php if (empty($recent_orders)): ?>
                <p class="text-muted">No orders yet</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Order Date</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                <td><?php echo number_format($order['total_price']); ?>đ</td>
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
                                       class="btn btn-sm btn-outline-primary">View</a>
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

<?php include '../includes/footer.php'; ?>