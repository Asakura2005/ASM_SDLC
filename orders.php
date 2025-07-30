<?php
$page_title = "Đơn hàng của tôi";
include '../fooddelivery/includes/header.php';
include '../fooddelivery/functions/auth.php';
include '../fooddelivery/functions/orders.php';


require_login();

$orders = get_user_orders($db, $_SESSION['user_id']);

// Handle rating submission
if ($_POST && isset($_POST['submit_rating'])) {
    $item_id = (int)$_POST['item_id'];
    $score = (int)$_POST['score'];
    $comment = trim($_POST['comment']);
    
    if ($item_id > 0 && $score >= 1 && $score <= 5) {
        // Check if user already rated this item
        $check_query = "SELECT rating_id FROM ratings WHERE user_id = ? AND item_id = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$_SESSION['user_id'], $item_id]);
        
        if ($check_stmt->rowCount() == 0) {
            $rating_query = "INSERT INTO ratings (user_id, item_id, score, comment) VALUES (?, ?, ?, ?)";
            $rating_stmt = $db->prepare($rating_query);
            if ($rating_stmt->execute([$_SESSION['user_id'], $item_id, $score, $comment])) {
                $_SESSION['success'] = 'Cảm ơn bạn đã đánh giá!';
            }
        }
    }
    header('Location: orders.php');
    exit();
}
?>

<div class="container my-4">
    <h1 class="mb-4">Đơn hàng của tôi</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
            <h4>Chưa có đơn hàng nào</h4>
            <p class="text-muted">Hãy đặt món ăn đầu tiên của bạn</p>
            <a href="menu.php" class="btn btn-primary">Xem thực đơn</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
        <div class="card mb-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0">Đơn hàng #<?php echo $order['order_id']; ?></h6>
                        <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge bg-<?php 
                            echo $order['status'] == 'delivered' ? 'success' : 
                                ($order['status'] == 'confirmed' ? 'primary' : 
                                ($order['status'] == 'cancelled' ? 'danger' : 'warning')); 
                        ?>">
                            <?php 
                            $status_text = [
                                'pending' => 'Chờ xác nhận',
                                'confirmed' => 'Đã xác nhận',
                                'delivered' => 'Đã giao',
                                'cancelled' => 'Đã hủy'
                            ];
                            echo $status_text[$order['status']];
                            ?>
                        </span>
                        <div class="mt-1">
                            <strong class="text-primary"><?php echo number_format($order['total_price']); ?>đ</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php 
                $order_items = get_order_items($db, $order['order_id']);
                foreach ($order_items as $item): 
                ?>
                <div class="row align-items-center border-bottom py-2">
                    <div class="col-md-2">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                             class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                        <small class="text-muted">Số lượng: <?php echo $item['quantity']; ?> × <?php echo number_format($item['price']); ?>đ</small>
                    </div>
                    <div class="col-md-2">
                        <strong><?php echo number_format($item['price'] * $item['quantity']); ?>đ</strong>
                    </div>
                    <div class="col-md-2">
                        <?php if ($order['status'] == 'delivered'): ?>
                            <?php
                            // Check if already rated
                            $rating_check = "SELECT rating_id FROM ratings WHERE user_id = ? AND item_id = ?";
                            $rating_stmt = $db->prepare($rating_check);
                            $rating_stmt->execute([$_SESSION['user_id'], $item['item_id']]);
                            $already_rated = $rating_stmt->rowCount() > 0;
                            ?>
                            
                            <?php if (!$already_rated): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" data-bs-target="#ratingModal<?php echo $item['item_id']; ?>">
                                <i class="fas fa-star me-1"></i>Đánh giá
                            </button>
                            
                            <!-- Rating Modal -->
                            <div class="modal fade" id="ratingModal<?php echo $item['item_id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Đánh giá món ăn</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                                <h6><?php echo htmlspecialchars($item['name']); ?></h6>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Điểm số (1-5 sao)</label>
                                                    <div class="rating-stars">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <input type="radio" name="score" value="<?php echo $i; ?>" 
                                                               id="star<?php echo $item['item_id']; ?>_<?php echo $i; ?>" required>
                                                        <label for="star<?php echo $item['item_id']; ?>_<?php echo $i; ?>" 
                                                               class="star-label">★</label>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="comment<?php echo $item['item_id']; ?>" class="form-label">Nhận xét</label>
                                                    <textarea class="form-control" name="comment" 
                                                              id="comment<?php echo $item['item_id']; ?>" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" name="submit_rating" class="btn btn-primary">Gửi đánh giá</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <small class="text-success">
                                <i class="fas fa-check me-1"></i>Đã đánh giá
                            </small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-stars input[type="radio"] {
    display: none;
}

.star-label {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.rating-stars input[type="radio"]:checked ~ .star-label,
.rating-stars input[type="radio"]:hover ~ .star-label,
.star-label:hover {
    color: #ffc107;
}
</style>

<?php include '../fooddelivery/includes/footer.php'; ?>