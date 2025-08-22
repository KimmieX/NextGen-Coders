<?php
require_once 'config.php';
require_once 'functions.php';

// Authentication functions for both admin and user

// Updated login function in auth.php
function login($username, $password, $remember = false, $isAdmin = false) {
    global $pdo;
    
    $table = $isAdmin ? 'admins' : 'users';
    // Changed to use absolute paths
    $redirect = $isAdmin ? '/chantmo-supa/admin/dashboard/' : '/chantmo-supa/pages/dashboard.php';
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // For users, check if email is verified
        if (!$isAdmin && !$user['email_verified']) {
            return ['status' => 'error', 'message' => 'Please verify your email before logging in.'];
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = $isAdmin;
        $_SESSION['logged_in'] = true;
        
        // Remember me cookie
        if ($remember) {
            $token = generateToken();
            $expiry = time() + 60 * 60 * 24 * 30; // 30 days
            
            // Store token in database
            $stmt = $pdo->prepare("UPDATE $table SET remember_token = ? WHERE id = ?");
            $stmt->execute([$token, $user['id']]);
            
            setcookie('remember_me', $token, $expiry, '/chantmo-supa/');
        }
        
        return ['status' => 'success', 'redirect' => $redirect];
    }
    
    return ['status' => 'error', 'message' => 'Invalid username or password.'];
}


// Password reset functions
function forgotPassword($email) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $resetToken = generateToken();
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
        $stmt->execute([$resetToken, $expires, $user['id']]);
        
        $resetLink = APP_URL . "/pages/auth/reset-password.php?token=" . $resetToken;
        $subject = "Password Reset Request for ChantMO Supermarket";
        $body = "Please click the following link to reset your password: <a href='$resetLink'>$resetLink</a> (expires in 1 hour)";
        
        if (sendEmail($email, $subject, $body)) {
            return ['status' => 'success', 'message' => 'Password reset link sent to your email.'];
        }
    }
    
    return ['status' => 'error', 'message' => 'If your email exists in our system, you will receive a password reset link.'];
}

function resetPassword($token, $newPassword) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        if (!isPasswordStrong($newPassword)) {
            return ['status' => 'error', 'message' => 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.'];
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
        $stmt->execute([$hashedPassword, $user['id']]);
        
        return ['status' => 'success', 'message' => 'Password reset successfully. You can now login with your new password.'];
    }
    
    return ['status' => 'error', 'message' => 'Invalid or expired token.'];
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

// Logout function
function logout() {
    global $pdo;
    
    if (isset($_SESSION['user_id'])) {
        $table = isAdmin() ? 'admins' : 'users';
        $stmt = $pdo->prepare("UPDATE $table SET remember_token = NULL WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    }
    
    // Unset all session variables
    $_SESSION = array();
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Delete remember me cookie
    setcookie('remember_me', '', time() - 3600, '/chantmo-supa/');
    
    // Destroy the session
    session_destroy();
    
    // Redirect to home page
    redirect('/chantmo-supa/index.php');
}
?>