<?php
$page_title = "Menu";
include '/xampp/htdocs/PHP/fooddelivery/includes/header.php';

// Get categories
$cat_query = "SELECT * FROM categories ORDER BY name";
$cat_stmt = $db->prepare($cat_query);
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$category_id = $_GET['category'] ?? '';

// Build query
$query = "SELECT mi.*, c.name as category_name, r.name as restaurant_name 
          FROM menu_items mi 
          LEFT JOIN categories c ON mi.category_id = c.category_id 
          LEFT JOIN restaurants r ON mi.restaurant_id = r.restaurant_id 
          WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (mi.name LIKE ? OR mi.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_id) {
    $query .= " AND mi.category_id = ?";
    $params[] = $category_id;
}

$query .= " ORDER BY mi.name";

$stmt = $db->prepare($query);
$stmt->execute($params);
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-4">
    <h1 class="text-center mb-4">Menu</h1>
    
    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="GET" class="d-flex">
                <input type="text" class="form-control me-2" name="search" 
                       placeholder="Search for dishes..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        <div class="col-md-4">
            <form method="GET">
                <?php if ($search): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <?php endif; ?>
                <select class="form-select" name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['category_id']; ?>" 
                                <?php echo $category_id == $cat['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>
    
    <!-- Menu Items -->
    <?php if (empty($menu_items)): ?>
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4>No dishes found</h4>
            <p class="text-muted">Try searching with a different keyword or selecting another category.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($menu_items as $item): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card food-card h-100 shadow-sm">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p class="card-text text-muted small"><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($item['category_name']); ?>
                                <span class="ms-2">
                                    <i class="fas fa-store me-1"></i><?php echo htmlspecialchars($item['restaurant_name']); ?>
                                </span>
                            </small>
                        </div>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0"><?php echo number_format($item['price']); ?>đ</span>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                <form method="POST" action="add_to_cart.php" class="d-inline">
                                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-cart-plus me-1"></i>Add
                                    </button>
                                </form>
                                <?php else: ?>
                                <a href="login.php" class="btn btn-outline-primary btn-sm">Login to buy</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '/xampp/htdocs/PHP/fooddelivery/includes/footer.php'; ?>