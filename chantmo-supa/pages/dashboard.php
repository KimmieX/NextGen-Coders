<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if user is logged in
if (!isLoggedIn() || isAdmin()) {
    redirect('/chantmo-supa/pages/auth/login.php');
}

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

require_once __DIR__ . '/../includes/header.php';
?>

<style>
    /* Dashboard Gradient Styles */
    .dashboard-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        background: white;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .dashboard-card .card-header {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        color: white;
        border-bottom: none;
        padding: 1rem 1.5rem;
        font-weight: 600;
    }
    
    .dashboard-card .card-title {
        color: #2d3748;
        font-weight: 700;
    }
    
    .nav-item .nav-link {
        color: #4a5568;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .nav-item .nav-link:hover,
    .nav-item .nav-link.active {
        background: linear-gradient(135deg, rgba(110, 142, 251, 0.1), rgba(167, 119, 227, 0.1));
        color: #6e8efb;
    }
    
    .nav-item .nav-link.active {
        font-weight: 600;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        border: none;
        padding: 0.6rem 1.5rem;
        font-weight: 500;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5d7df4, #9a6bdb);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(110, 142, 251, 0.3);
    }
    
    .btn-outline-primary {
        border: 2px solid transparent;
        background: linear-gradient(white, white) padding-box,
                    linear-gradient(135deg, #6e8efb, #a777e3) border-box;
        color: #2d3748;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        background: linear-gradient(white, white) padding-box,
                    linear-gradient(135deg, #5d7df4, #9a6bdb) border-box;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(110, 142, 251, 0.1);
    }
    
    .welcome-message {
        background: linear-gradient(135deg, rgba(110, 142, 251, 0.05), rgba(167, 119, 227, 0.05));
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        border-left: 4px solid #6e8efb;
    }
    
    .account-info dt {
        font-weight: 600;
        color: #4a5568;
    }
    
    .account-info dd {
        color: #2d3748;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .dashboard-card {
            margin-bottom: 1.5rem;
        }
        
        .welcome-message {
            padding: 1.5rem;
        }
    }
</style>

<main class="container py-5">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="dashboard-card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Navigation</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="/chantmo-supa/pages/dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/chantmo-supa/featured-products.php">
                                <i class="fas fa-shopping-bag me-2"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/chantmo-supa/orders.php">
                                <i class="fas fa-receipt me-2"></i> My Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/chantmo-supa/pages/wishlist.php">
                                <i class="fas fa-heart me-2"></i> Wishlist
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/chantmo-supa/pages/profile.php">
                                <i class="fas fa-user me-2"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/chantmo-supa/pages/auth/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="dashboard-card">
                <div class="card-body">
                    <div class="welcome-message">
                        <h2 class="fw-bold mb-3">Welcome back, <?= htmlspecialchars($user['username']) ?>!</h2>
                        <p class="lead text-muted mb-0">Happy shopping at ChantMO Supermarket!</p>
                    </div>
                    
                    <?php if (!$user['email_verified']): ?>
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
                            <div>
                                <h5 class="alert-heading mb-1">Email Not Verified</h5>
                                <p class="mb-0">Please check your inbox for the verification email.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="dashboard-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i> Account Information</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row account-info">
                                <dt class="col-sm-3">Username:</dt>
                                <dd class="col-sm-9"><?= htmlspecialchars($user['username']) ?></dd>
                                
                                <dt class="col-sm-3">Email:</dt>
                                <dd class="col-sm-9"><?= htmlspecialchars($user['email']) ?></dd>
                                
                                <dt class="col-sm-3">Member Since:</dt>
                                <dd class="col-sm-9"><?= date('F j, Y', strtotime($user['created_at'])) ?></dd>
                                
                                <dt class="col-sm-3">Last Login:</dt>
                                <dd class="col-sm-9"><?= date('F j, Y \a\t g:i A', strtotime($user['last_login'] ?? $user['created_at'])) ?></dd>
                            </dl>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i> Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-3">
                                <a href="/chantmo-supa/featured-products.php" class="btn btn-primary">
                                    <i class="fas fa-shopping-bag me-2"></i> Browse Products
                                </a>
                                <a href="/chantmo-supa/orders.php" class="btn btn-primary">
                                    <i class="fas fa-receipt me-2"></i> View Orders
                                </a>
                                <a href="/chantmo-supa/pages/profile.php" class="btn btn-outline-primary">
                                    <i class="fas fa-user-edit me-2"></i> Edit Profile
                                </a>
                                <a href="/chantmo-supa/pages/wishlist.php" class="btn btn-outline-primary">
                                    <i class="fas fa-heart me-2"></i> View Wishlist
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>