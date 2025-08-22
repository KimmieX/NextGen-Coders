<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Ensure we're sending JSON header
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['email'])) {
        throw new Exception('Email is required');
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please enter a valid email address');
    }

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'info', 
            'message' => 'You are already subscribed to our newsletter!'
        ]);
        exit;
    }

    // Insert new subscriber
    $token = generateToken();
    $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, verification_token) VALUES (?, ?)");
    $stmt->execute([$email, $token]);

    // Send verification email
    $verificationLink = APP_URL . "/verify-newsletter.php?token=" . $token;
    $subject = "Confirm your ChantMO Newsletter Subscription";
    $body = "Please click the link below to confirm your subscription:<br><br>"
          . "<a href='$verificationLink' style='background:#6e8efb; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;'>Confirm Subscription</a><br><br>"
          . "The link will expire in 24 hours.";
    
    if (sendEmail($email, $subject, $body)) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Please check your email to confirm your subscription!'
        ]);
    } else {
        throw new Exception('Failed to send verification email. Please try again.');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}