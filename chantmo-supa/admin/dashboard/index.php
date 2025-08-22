<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/Product.php';
require_once __DIR__ . '/../../includes/Order.php';

// Check if admin is logged in
if (!isLoggedIn() || !isAdmin()) {
    redirect(base_url('admin/login.php'));
}

// Get counts for dashboard
$productCount = Product::count();
$orderCount = Order::count();
$pendingOrderCount = Order::count(['status' => 'pending']);
$todayOrderCount = Order::count(['DATE(created_at)' => date('Y-m-d')]);
$customerCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Use the new admin header
require_once __DIR__ . '/../includes/admin_header.php';
?>

<!-- Dashboard widgets -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Total Products</h5>
                <h2 class="card-text"><?= $productCount ?></h2>
                <a href="<?= base_url('admin/products/') ?>" class="text-white">View all</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h5 class="card-title">Total Orders</h5>
                <h2 class="card-text"><?= $orderCount ?></h2>
                <a href="<?= base_url('admin/orders.php') ?>" class="text-white">View all</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title">Pending Orders</h5>
                <h2 class="card-text"><?= $pendingOrderCount ?></h2>
                <a href="<?= base_url('admin/orders.php') ?>" class="text-white">View all</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info h-100">
            <div class="card-body">
                <h5 class="card-title">Today's Orders</h5>
                <h2 class="card-text"><?= $todayOrderCount ?></h2>
                <a href="<?= base_url('admin/orders.php') ?>" class="text-white">View all</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
    <div class="card text-white bg-secondary h-100">
        <div class="card-body">
            <h5 class="card-title">Total Customers</h5>
            <h2 class="card-text"><?= $customerCount ?></h2>
            <a href="<?= base_url('admin/customers/') ?>" class="text-white">View all</a>
        </div>
    </div>
</div>
    
</div>

<?php 
// Use the new admin footer
require_once __DIR__ . '/../includes/admin_footer.php'; 
?>