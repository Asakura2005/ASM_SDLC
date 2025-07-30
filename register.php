<?php
$page_title = "Đăng ký";
include '../fooddelivery/includes/header.php';
include '../fooddelivery/functions/auth.php';

$error = '';
$success = '';

if ($_POST) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Validation
    if (empty($username) || empty($password) || empty($email) || empty($full_name) || empty($confirm_password) || empty($phone)) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
    } elseif (strlen($username) < 3) {
        $error = 'Tên đăng nhập phải có ít nhất 3 ký tự';
    } elseif (!empty($phone) && strlen($phone) != 10){
        $error = 'Số điện thoại không hợp lệ';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp';    
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ';
    } else {
        $result = register_user($db, $username, $password, $email, $full_name, $phone);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Đăng ký tài khoản</h2>
                    
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
                    
                    <form method="POST" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập *</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                                   required minlength="3">
                            <div class="invalid-feedback">Vui lòng nhập tên đăng nhập (ít nhất 3 ký tự)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên *</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                            <div class="invalid-feedback">Vui lòng nhập họ và tên</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu *</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   required minlength="6">
                            <div class="invalid-feedback">Mật khẩu phải có ít nhất 6 ký tự</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu *</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div class="invalid-feedback">Vui lòng xác nhận mật khẩu</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Đăng ký</button>
                        
                        <div class="text-center">
                            <p>Đã có tài khoản? <a href="login.php" class="text-primary">Đăng nhập ngay</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Bootstrap form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByTagName('form');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

document.getElementById('username').addEventListener('input', function() {
    if (this.value.length < 3) {
        this.setCustomValidity('Tên đăng nhập phải có ít nhất 3 ký tự');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('email').addEventListener('input', function() {
    if (!this.value.includes('@')) {
        this.setCustomValidity('Email không hợp lệ');
    } else {
        this.setCustomValidity('');
    }
});
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Mật khẩu xác nhận không khớp');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php include '../fooddelivery/includes/footer.php'; ?>