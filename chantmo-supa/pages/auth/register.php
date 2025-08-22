<?php
$pageTitle = 'Register';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

if (isLoggedIn()) {
    redirect('/chantmo-supa/pages/dashboard.php');
}

// Initialize variables
$error = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    
    // Validate
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $result = register($username, $email, $password);
        
        if ($result['status'] === 'success') {
            // Send verification email
            $verificationLink = APP_URL . "/pages/auth/verify-email.php?token=" . $result['token'];
            $subject = "Verify Your Email for " . APP_NAME;
            $body = "
                <h2>Welcome to " . APP_NAME . "!</h2>
                <p>Thank you for registering. Please verify your email address:</p>
                <p><a href='$verificationLink'>Verify My Email</a></p>
                <p>If you didn't create an account, please ignore this email.</p>
                <p>Best regards,<br>" . APP_NAME . " Team</p>
            ";

            if (sendEmail($email, $subject, $body)) {
                redirect('/chantmo-supa/pages/auth/login.php', 'Registration successful! Please check your email to verify your account.');
            } else {
                $error = "Registration successful but failed to send verification email. Please contact support.";
            }
        } else {
            $error = $result['message'];
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<main class="auth-wrapper">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="auth-card">
                    <div class="auth-header">
                        <h1 class="auth-title mb-3">Create Account</h1>
                        <!-- Added subtitle for consistency -->
                        <p class="auth-subtitle">Set up your new account</p>
                    </div>
                    
                    <!-- Changed to use auth-body without compact class since register has more content -->
                    <div class="auth-body">
                        <?php displayMessage(); ?>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="invalid-feedback">Please choose a username.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="invalid-feedback">Please provide a valid email.</div>
                            </div>
                            
                            <div class="mb-3 password-toggle-container">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           required onkeyup="checkPasswordStrength(this.value)">
                                    <span class="input-group-text bg-transparent toggle-password">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <div id="password-strength" class="strength-meter mt-2">
                                    <div class="strength-meter-fill"></div>
                                </div>
                                <div class="invalid-feedback">Please provide a password.</div>
                            </div>
                            
                            <div class="mb-3 password-toggle-container">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="confirm_password" 
                                           name="confirm_password" required>
                                </div>
                                <div class="invalid-feedback">Passwords must match.</div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="showPasswords">
                                <label class="form-check-label" for="showPasswords">Show Passwords</label>
                            </div>
                            
                            <button type="submit" class="btn btn-auth w-100 py-3">
                                <i class="fas fa-user-plus me-2"></i> Register
                            </button>
                        </form>
                        
                        <div class="auth-footer mt-4 text-center">
                            <p>Already have an account? <a href="/chantmo-supa/pages/auth/login.php" class="auth-link">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Enhanced password toggle
function togglePassword() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('confirm_password');
    const checkbox = document.getElementById('showPasswords');
    const eyeIcon = document.querySelector('.toggle-password i');
    
    if (checkbox.checked) {
        passwordField.type = 'text';
        confirmField.type = 'text';
        eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordField.type = 'password';
        confirmField.type = 'password';
        eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Click handler for the eye icon
document.querySelector('.toggle-password').addEventListener('click', function() {
    const checkbox = document.getElementById('showPasswords');
    checkbox.checked = !checkbox.checked;
    togglePassword();
});

// Enhanced password strength checker
function checkPasswordStrength(password) {
    const strengthMeter = document.querySelector('.strength-meter-fill');
    
    // Reset
    strengthMeter.className = 'strength-meter-fill';
    strengthMeter.style.width = '0%';
    
    if (password.length === 0) return;
    
    // Check strength
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    
    let strength = 0;
    
    if (password.length > 7) strength++;
    if (hasUpperCase) strength++;
    if (hasLowerCase) strength++;
    if (hasNumbers) strength++;
    if (hasSpecialChars) strength++;
    
    if (strength < 3) {
        strengthMeter.classList.add('strength-weak');
    } else if (strength < 5) {
        strengthMeter.classList.add('strength-medium');
    } else {
        strengthMeter.classList.add('strength-strong');
    }
}

// Form validation
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>