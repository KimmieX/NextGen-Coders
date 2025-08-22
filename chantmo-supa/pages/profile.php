<?php
$pageTitle = 'Profile';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('/chantmo-supa/pages/auth/login.php');
}

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle form submissions
$error = null;
$success = null;

// Update profile info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$phone, $address, $_SESSION['user_id']]);
        
        $success = "Profile updated successfully!";
    } catch (PDOException $e) {
        $error = "Failed to update profile: " . $e->getMessage();
    }
}

// Change password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Verify current password
    if (!password_verify($currentPassword, $user['password'])) {
        $error = "Current password is incorrect";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "New passwords don't match";
    } elseif (!isPasswordStrong($newPassword)) {
        $error = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
    } else {
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $_SESSION['user_id']]);
        
        $success = "Password changed successfully!";
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<style>
    /* Modern Profile Layout */
    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .profile-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        margin: 0 auto 1.5rem;
        box-shadow: 0 5px 15px rgba(110, 142, 251, 0.3);
    }
    
    .profile-name {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .profile-meta {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .profile-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f1f3f5;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 0.75rem;
        color: #6e8efb;
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #6e8efb;
        box-shadow: 0 0 0 0.25rem rgba(110, 142, 251, 0.15);
    }
    
    .btn-save {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        border: none;
        padding: 0.75rem 2rem;
        font-weight: 500;
        border-radius: 8px;
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-save:hover {
        background: linear-gradient(135deg, #5d7df4, #9a6bdb);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(110, 142, 251, 0.3);
        color: white;
    }
    
    .alert-message {
        border-radius: 8px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
    }
    
    .alert-message i {
        margin-right: 0.75rem;
        font-size: 1.2rem;
    }
    
    .alert-success {
        background: rgba(40, 167, 69, 0.1);
        border-left: 4px solid #28a745;
        color: #28a745;
    }
    
    .alert-danger {
        background: rgba(220, 53, 69, 0.1);
        border-left: 4px solid #dc3545;
        color: #dc3545;
    }
    
    /* Password strength indicator */
    .password-strength {
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
        margin-top: 0.5rem;
        overflow: hidden;
    }
    
    .password-strength-bar {
        height: 100%;
        width: 0;
        transition: width 0.3s ease;
    }
    
    .strength-weak {
        background: #dc3545;
        width: 30%;
    }
    
    .strength-medium {
        background: #fd7e14;
        width: 60%;
    }
    
    .strength-strong {
        background: #28a745;
        width: 100%;
    }
    
    .strength-text {
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .profile-container {
            padding: 1.5rem 0.5rem;
        }
        
        .profile-section {
            padding: 1.5rem;
        }
    }
</style>

<main class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fas fa-user"></i>
        </div>
        <h1 class="profile-name"><?= htmlspecialchars($user['username']) ?></h1>
        <div class="profile-meta">
            <span><i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($user['email']) ?></span> • 
            <span class="ms-2"><i class="fas fa-calendar-alt me-1"></i> Member since <?= date('M Y', strtotime($user['created_at'])) ?></span>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="alert-message alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <div><?= $error ?></div>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert-message alert-success">
            <i class="fas fa-check-circle"></i>
            <div><?= $success ?></div>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-6">
            <div class="profile-section">
                <h2 class="section-title">
                    <i class="fas fa-user-cog"></i> Profile Information
                </h2>
                
                <form method="POST" action="">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="mb-4">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="profile-section">
                <h2 class="section-title">
                    <i class="fas fa-lock"></i> Security Settings
                </h2>
                
                <form method="POST" action="">
                    <input type="hidden" name="change_password" value="1">
                    
                    <div class="mb-4">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required
                               onkeyup="checkPasswordStrength(this.value)">
                        <div class="password-strength">
                            <div class="password-strength-bar" id="password-strength-bar"></div>
                        </div>
                        <div class="strength-text" id="password-strength-text"></div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-key me-2"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>
            
            
        </div>
    </div>
</main>

<script>
function checkPasswordStrength(password) {
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    
    // Reset
    strengthBar.className = 'password-strength-bar';
    strengthText.textContent = '';
    
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
        strengthBar.classList.add('strength-weak');
        strengthText.textContent = 'Weak password';
        strengthText.className = 'strength-text text-danger';
    } else if (strength < 5) {
        strengthBar.classList.add('strength-medium');
        strengthText.textContent = 'Medium strength';
        strengthText.className = 'strength-text text-warning';
    } else {
        strengthBar.classList.add('strength-strong');
        strengthText.textContent = 'Strong password';
        strengthText.className = 'strength-text text-success';
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>