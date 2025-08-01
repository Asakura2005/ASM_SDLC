<?php
ob_start(); // Add this line at the very beginning of the file
$page_title = "Shopping Cart";
include '../fooddelivery/includes/header.php';
include '../fooddelivery/functions/auth.php';
include_once '../fooddelivery/functions/cart.php';

require_login();

$cart_items = get_cart_items($db);
$total = get_cart_total($db);

// Handle cart updates
if ($_POST) {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantities'] as $item_id => $quantity) {
            update_cart($item_id, (int)$quantity);
        }
        header('Location: cart.php');
        exit();
    }
    
    if (isset($_POST['remove_item'])) {
        remove_from_cart($_POST['item_id']);
        header('Location: cart.php');
        exit();
    }
}
?>

<div class="container my-4">
    <h1 class="mb-4">Your Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h4>Your cart is empty</h4>
            <p class="text-muted">Add some delicious items to your cart.</p>
            <a href="menu.php" class="btn btn-primary">View Menu</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="row align-items-center border-bottom py-3">
                                <div class="col-md-2">
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                         class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted"><?php echo number_format($item['price']); ?>đ</small>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="number" class="form-control" 
                                               name="quantities[<?php echo $item['item_id']; ?>]" 
                                               value="<?php echo $item['quantity']; ?>" min="1" max="99">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <strong><?php echo number_format($item['subtotal']); ?>đ</strong>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" name="remove_item" value="1" 
                                            class="btn btn-sm btn-outline-danger">
                                        <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="text-end mt-3">
                                <button type="submit" name="update_cart" class="btn btn-outline-primary">
                                    <i class="fas fa-sync me-1"></i>Update Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span><?php echo number_format($total); ?>đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping Fee:</span>
                                <span class="text-success">Free</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong class="text-primary"><?php echo number_format($total); ?>đ</strong>
                            </div>
                            
                            <a href="checkout.php" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-credit-card me-1"></i>Checkout
                            </a>
                            <a href="menu.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-arrow-left me-1"></i>Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>