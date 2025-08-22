<?php
$pageTitle = 'Login';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

if (isLoggedIn()) {
    redirect('/chantmo-supa/dashboard.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);
    
    $result = login($username, $password, $remember, false);
    
    if ($result['status'] === 'success') {
        redirect($result['redirect']);
    } else {
        $error = $result['message'];
    }
}

// Check for remember me cookie
if (isset($_COOKIE['remember_me']) && !isLoggedIn()) {
    $token = $_COOKIE['remember_me'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = false;
        $_SESSION['logged_in'] = true;
        
        redirect('/chantmo-supa/pages/dashboard.php');
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
                        <h1 class="auth-title">Login</h1>
                    </div>
                    
                    <div class="auth-body auth-body--compact">
                        <?php displayMessage(); ?>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username or Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            
                            <div class="mb-3 password-toggle-container">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <span class="input-group-text bg-transparent toggle-password">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="showPassword">
                                    <label class="form-check-label" for="showPassword">Show Password</label>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            
                            <button type="submit" class="btn btn-auth w-100 py-3">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </button>
                        </form>
                        
                        <div class="auth-footer mt-4">
                            <p class="mb-2"><a href="/chantmo-supa/pages/auth/register.php" class="auth-link">Create account</a></p>
                            <p class="mb-2"><a href="/chantmo-supa/pages/auth/forgot-password.php" class="auth-link">Forgot password?</a></p>
                            <p><a href="/chantmo-supa/admin/login.php" class="auth-link">Admin login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Enhanced password toggle
document.getElementById('showPassword').addEventListener('change', function() {
    const passwordField = document.getElementById('password');
    const eyeIcon = document.querySelector('.toggle-password i');
    
    if (this.checked) {
        passwordField.type = 'text';
        eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordField.type = 'password';
        eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
});

// Click handler for the eye icon
document.querySelector('.toggle-password').addEventListener('click', function() {
    const checkbox = document.getElementById('showPassword');
    checkbox.checked = !checkbox.checked;
    checkbox.dispatchEvent(new Event('change'));
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>