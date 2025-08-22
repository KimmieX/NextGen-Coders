<?php
require_once 'config.php';

// Load PHPMailer classes
require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Function to redirect with message
function redirect($url, $message = null) {
    if ($message) {
        $_SESSION['message'] = $message;
    }
    header("Location: $url");
    exit();
}

// Function to display messages
function displayMessage() {
    if (isset($_SESSION['message'])) {
        echo '<div class="message">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
}

// Password strength checker
function isPasswordStrong($password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special char
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

// Generate random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Send email function
function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->Port       = MAIL_PORT;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        
        // Only show debug output if explicitly enabled
        if (defined('MAIL_DEBUG') && MAIL_DEBUG) {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        } else {
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
        }
        
        // Recipients
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
        return false;
    }
}

// Register function (for users only)
function register($username, $email, $password) {
    global $pdo;
    
    // Check if username or email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->rowCount() > 0) {
        return ['status' => 'error', 'message' => 'Username or email already exists.'];
    }
    
    // Validate password strength
    if (!isPasswordStrong($password)) {
        return ['status' => 'error', 'message' => 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.'];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $verificationToken = generateToken();
    $verificationExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    // Insert user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, verification_token, verification_token_expires) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $email, $hashedPassword, $verificationToken, $verificationExpires]);
    
    return [
        'status' => 'success',
        'token' => $verificationToken,
        'message' => 'Registration successful! Please check your email to verify your account.'
    ];
}

// Add this to your existing functions.php
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function base_url($path = '') {
    return BASE_PATH . '/' . ltrim($path, '/');
}

function asset_url($path = '') {
    return BASE_PATH . '/assets/' . ltrim($path, '/');
}
?>