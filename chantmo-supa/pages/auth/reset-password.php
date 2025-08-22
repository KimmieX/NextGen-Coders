<?php
$pageTitle = 'Reset Password';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

if (!isset($_GET['token'])) {
    redirect('/chantmo-supa/pages/auth/login.php', 'Invalid reset token.');
}

$token = $_GET['token'];

// Check if token is valid
$stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    redirect('/chantmo-supa/pages/auth/login.php', 'Invalid or expired reset token.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    
    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $result = resetPassword($token, $newPassword);
        
        if ($result['status'] === 'success') {
            redirect('/chantmo-supa/pages/auth/login.php', $result['message']);
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
                        <h1 class="auth-title mb-3">Reset Password</h1>
                        <p class="auth-subtitle">Create a new password for your account</p>
                    </div>
                    
                    <div class="auth-body">
                        <?php displayMessage(); ?>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3 password-toggle-container">
                                <label for="password" class="form-label">New Password</label>
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
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
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
                                <i class="fas fa-key me-2"></i> Reset Password
                            </button>
                        </form>
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