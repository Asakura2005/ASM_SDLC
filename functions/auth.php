<?php
function register_user($db, $username, $password, $email, $full_name, $phone) {
    try {
        // Check if username or email already exists
        $check_query = "SELECT user_id FROM users WHERE username = :username OR email = :email";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':username', $username);
        $check_stmt->bindParam(':email', $email);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Username or email already exists.'];
        }
        
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $query = "INSERT INTO users (username, password_hash, email, full_name, phone, role) 
                  VALUES (:username, :password_hash, :email, :full_name, :phone, 'customer')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':phone', $phone);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration successful.'];
        } else {
            return ['success' => false, 'message' => 'An error occurred during registration.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'System error: ' . $e->getMessage()];
    }
}

function login_user($db, $username, $password) {
    try {
        $query = "SELECT user_id, username, password_hash, email, full_name, phone, role 
                  FROM users WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                return ['success' => true, 'message' => 'Login successful.'];
            }
        }
        
        return ['success' => false, 'message' => 'Incorrect username or password.'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'System error: ' . $e->getMessage()];
    }
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

function require_admin() {
    if (!is_admin()) {
        header('Location: index.php');
        exit();
    }
}
?>