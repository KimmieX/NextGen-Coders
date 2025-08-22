<?php
$pageTitle = 'Admin Login';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if already logged in
if (isLoggedIn() && isAdmin()) {
    redirect('/chantmo-supa/admin/dashboard/');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);
    
    $result = login($username, $password, $remember, true);
    
    if ($result['status'] === 'success') {
        redirect($result['redirect']);
    } else {
        $error = $result['message'];
    }
}

// Check for remember me cookie
if (isset($_COOKIE['remember_me']) && !isLoggedIn()) {
    $token = $_COOKIE['remember_me'];
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE remember_token = ?");
    $stmt->execute([$token]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['email'] = $admin['email'];
        $_SESSION['is_admin'] = true;
        $_SESSION['logged_in'] = true;
        
        redirect('/chantmo-supa/admin/dashboard/');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Admin Login</h2>
                    
                    <?php displayMessage(); ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username or Email</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="showPassword" onclick="togglePassword()">
                                <label class="form-check-label" for="showPassword">Show Password</label>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function togglePassword() {
    const passwordField = document.getElementById('password');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
    } else {
        passwordField.type = 'password';
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>