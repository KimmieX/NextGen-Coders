<?php
$pageTitle = 'Admin Settings';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(base_url('admin/login.php'));
}

$current_page = 'settings.php';

// Get current admin data
$adminId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

// Initialize messages
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

// Update Profile Info
if (isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    
    if (empty($username) || empty($email)) {
        $_SESSION['error'] = "Username and email are required.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$username, $email, $adminId]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Username or email already exists.";
        } else {
            $stmt = $pdo->prepare("UPDATE admins SET username = ?, email = ?, updated_at = NOW() WHERE id = ?");
            if ($stmt->execute([$username, $email, $adminId])) {
                $_SESSION['message'] = "Profile updated successfully!";
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $admin['username'] = $username;
                $admin['email'] = $email;
            } else {
                $_SESSION['error'] = "Failed to update profile. Please try again.";
            }
        }
    }
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit();
}

// Change Password
if (isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (!password_verify($currentPassword, $admin['password'])) {
        $_SESSION['error'] = "Current password is incorrect.";
    } elseif ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "New passwords do not match.";
    } elseif (!isPasswordStrong($newPassword)) {
        $_SESSION['error'] = "Password must be at least 8 characters with uppercase, lowercase, number and special character.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
        if ($stmt->execute([$hashedPassword, $adminId])) {
            $_SESSION['message'] = "Password changed successfully!";
        } else {
            $_SESSION['error'] = "Failed to update password. Please try again.";
        }
    }
    header("Location: ".$_SERVER['REQUEST_URI']."#password");
    exit();
}

// Add New Admin
if (isset($_POST['add_admin'])) {
    $newUsername = trim($_POST['new_username']);
    $newEmail = trim($_POST['new_email']);
    $newPassword = $_POST['new_admin_password'];
    $confirmPassword = $_POST['confirm_new_admin_password'];
    
    if (empty($newUsername) || empty($newEmail) || empty($newPassword)) {
        $_SESSION['error'] = "All fields are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match.";
    } elseif (!isPasswordStrong($newPassword)) {
        $_SESSION['error'] = "Password must be at least 8 characters with uppercase, lowercase, number and special character.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
        $stmt->execute([$newUsername, $newEmail]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Username or email already exists.";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$newUsername, $newEmail, $hashedPassword])) {
                $_SESSION['message'] = "New admin added successfully!";
            } else {
                $_SESSION['error'] = "Failed to add new admin. Please try again.";
            }
        }
    }
    header("Location: ".$_SERVER['REQUEST_URI']."#add-admin");
    exit();
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Admin Settings</h6>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <ul class="nav nav-tabs mb-3" id="settingsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">Profile</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">Change Password</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="add-admin-tab" data-bs-toggle="tab" data-bs-target="#add-admin" type="button" role="tab">Add Admin</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="settingsTabContent">
                        <!-- Profile Tab -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                        
                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" toggle="#current_password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" toggle="#new_password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">Password must be at least 8 characters with uppercase, lowercase, number and special character.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" toggle="#confirm_password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                            </form>
                        </div>
                        
                        <!-- Add Admin Tab -->
                        <div class="tab-pane fade" id="add-admin" role="tabpanel">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="new_username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="new_username" name="new_username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="new_email" name="new_email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_admin_password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_admin_password" name="new_admin_password" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" toggle="#new_admin_password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">Password must be at least 8 characters with uppercase, lowercase, number and special character.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_new_admin_password" class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_new_admin_password" name="confirm_new_admin_password" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" toggle="#confirm_new_admin_password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" name="add_admin" class="btn btn-primary">Add Admin</button>
                            </form>
                            
                            <hr class="my-4">
                            
                            <h5>Existing Admins</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $pdo->query("SELECT username, email, created_at FROM admins ORDER BY created_at DESC");
                                        while ($row = $stmt->fetch()):
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['username']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle tab switching from URL hash
    if (window.location.hash) {
        const hash = window.location.hash;
        $('.nav-tabs button[data-bs-target="' + hash + '"]').tab('show');
    }

    // Password toggle functionality
    $('.toggle-password').click(function() {
        const input = $($(this).attr('toggle'));
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
});
</script>

<?php 
require_once __DIR__ . '/includes/admin_footer.php'; 
?>