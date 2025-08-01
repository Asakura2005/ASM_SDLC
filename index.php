<?php
$page_title = "Home";
include '../fooddelivery/includes/header.php';

// Get featured items
$query = "SELECT mi.*, c.name as category_name, r.name as restaurant_name 
          FROM menu_items mi 
          LEFT JOIN categories c ON mi.category_id = c.category_id 
          LEFT JOIN restaurants r ON mi.restaurant_id = r.restaurant_id 
          ORDER BY RAND() LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$featured_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <!-- Hero Section -->
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold text-primary">Fast & Reliable Delivery</h1>
            <p class="lead">Enjoy the best dishes from top-rated restaurants, delivered to your door in 30 minutes.</p>
            <a href="menu.php" class="btn btn-primary btn-lg">
                <i class="fas fa-utensils me-2"></i>View Menu
            </a>
        </div>
        <div class="col-lg-6">
            <img src="https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg" 
                 class="img-fluid rounded shadow" alt="Food delivery">
        </div>
    </div>

    <!-- Features -->
    <div class="row mb-5">
        <div class="col-md-4 text-center mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                    <h5>Fast Delivery</h5>
                    <p class="text-muted">Get your order within 30 minutes</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-center mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-star fa-3x text-primary mb-3"></i>
                    <h5>High Quality</h5>
                    <p class="text-muted">Fresh, delicious, and hygienic food</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-center mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-tags fa-3x text-primary mb-3"></i>
                    <h5>Affordable Prices</h5>
                    <p class="text-muted">Great deals and discount codes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Items -->
    <h2 class="text-center mb-4">Featured Dishes</h2>
    <div class="row">
        <?php foreach ($featured_items as $item): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card food-card h-100 shadow-sm">
                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                     class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                    <p class="card-text text-muted small"><?php echo htmlspecialchars($item['description']); ?></p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0"><?php echo number_format($item['price']); ?>đ</span>
                            <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="POST" action="add_to_cart.php" class="d-inline">
                                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-4">
        <a href="menu.php" class="btn btn-outline-primary btn-lg">View All Dishes</a>
    </div>
</div>

<?php include '/xampp/htdocs/PHP/fooddelivery/includes/footer.php'; ?>