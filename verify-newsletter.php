<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isset($_GET['token'])) {
    $_SESSION['message'] = 'Invalid verification token';
    redirect('/chantmo-supa/index.php');
}

$token = $_GET['token'];

try {
    // Verify token and mark as verified
    $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET is_verified = 1, verification_token = NULL WHERE verification_token = ?");
    $stmt->execute([$token]);
    
    if ($stmt->rowCount() > 0) {
        // Get the verified email
        $stmt = $pdo->prepare("SELECT email FROM newsletter_subscribers WHERE verification_token IS NULL AND is_verified = 1 LIMIT 1");
        $stmt->execute();
        $subscriber = $stmt->fetch();
        
        if ($subscriber) {
            // Send welcome email with discount code
            $subject = "Welcome to ChantMO Newsletter!";
            $body = "Thank you for subscribing! Here's your exclusive 20% discount code: <strong>CHANTMO20</strong><br><br>"
                  . "Use this code at checkout for your first order.";
            sendEmail($subscriber['email'], $subject, $body);
        }
        
        // Redirect with success parameter
        redirect('/chantmo-supa/index.php?newsletter_verified=1');
    } else {
        $_SESSION['message'] = 'Invalid or expired token';
        redirect('/chantmo-supa/index.php');
    }
} catch (PDOException $e) {
    error_log("Verification Error: " . $e->getMessage());
    $_SESSION['message'] = 'An error occurred during verification';
    redirect('/chantmo-supa/index.php');
}