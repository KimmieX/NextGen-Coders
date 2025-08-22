<?php
// Load PHPMailer
require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Database configuration
$host = 'localhost';
$db   = 'chantmo';
$user = 'chantmo_user';
$pass = 'StrongPassword123!';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Create database connection
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Verify connection works
    $pdo->query("SELECT 1");
} catch (\PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Service temporarily unavailable. Please try again later.");
}


// Mailtrap configuration
define('MAIL_HOST', 'sandbox.smtp.mailtrap.io');
define('MAIL_PORT', 2525);
define('MAIL_USERNAME', '962b343cc2b5ae');
define('MAIL_PASSWORD', '3a83797f770ebb');
define('MAIL_FROM', 'no-reply@chantmo.com');
define('MAIL_FROM_NAME', 'ChantMO Supermarket');
define('MAIL_DEBUG', false);  //

// Application settings
define('APP_NAME', 'ChantMO Supermarket');
define('DEBUG_MODE', true);

// Error reporting - consolidated
ini_set('display_errors', DEBUG_MODE ? 1 : 0);
ini_set('display_startup_errors', DEBUG_MODE ? 1 : 0);
error_reporting(DEBUG_MODE ? E_ALL : 0);

// Session configuration
session_start();



// Path configuration
define('BASE_PATH', '/chantmo-supa');
define('APP_URL', 'http://' . $_SERVER['HTTP_HOST'] . BASE_PATH);

// Initialize Product class with PDO connection
require_once __DIR__ . '/Product.php';
Product::setPDO($pdo);  // THIS IS CRUCIAL FOR YOUR ERROR
?>