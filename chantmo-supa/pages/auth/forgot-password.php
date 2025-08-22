<?php
$pageTitle = 'Forgot Password';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

if (isLoggedIn()) {
    redirect('/chantmo-supa/pages/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    // Generate reset token
    $resetToken = generateToken();
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Update user record
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
    $stmt->execute([$resetToken, $expires, $email]);
    
    if ($stmt->rowCount() > 0) {
        $resetLink = APP_URL . "/pages/auth/reset-password.php?token=" . $resetToken;
        $subject = "Password Reset Request for " . APP_NAME;
        $body = "
            <h2>Password Reset Request</h2>
            <p>Click this link to reset your password:</p>
            <p><a href='$resetLink'>Reset Password</a></p>
            <p><small>This link expires in 1 hour. If you didn't request this, please ignore this email.</small></p>
        ";

        if (sendEmail($email, $subject, $body)) {
            $message = "If your email exists in our system, you'll receive a password reset link.";
        } else {
            $error = "Failed to send email. Please try again later.";
        }
    } else {
        $message = "If your email exists in our system, you'll receive a password reset link.";
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<main class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="auth-card">
                    <!-- Header with tighter spacing -->
                    <div class="auth-header pb-3">  <!-- Reduced padding -->
                        <h1 class="auth-title mb-2">Forgot Password</h1>  <!-- Reduced margin -->
                        <p class="auth-subtitle mb-0">Enter your email to reset your password</p>  <!-- No bottom margin -->
                    </div>
                    
                    <!-- Compact body for this simple form -->
                    <div class="auth-body auth-body--compact pt-3">  <!-- Reduced top padding -->
                        <?php displayMessage(); ?>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger mb-3"><?= htmlspecialchars($error) ?></div>  <!-- Added bottom margin -->
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">  <!-- Standard Bootstrap spacing -->
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-auth w-100 mt-3 py-2">  <!-- Adjusted top margin and padding -->
                                <i class="fas fa-paper-plane me-2"></i> Send Reset Link
                            </button>
                        </form>
                        
                        <!-- Footer link with tighter spacing -->
                        <div class="auth-footer mt-3 text-center">  <!-- Reduced top margin -->
                            <a href="/chantmo-supa/pages/auth/login.php" class="auth-link">
                                <i class="fas fa-arrow-left me-2"></i> Back to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>