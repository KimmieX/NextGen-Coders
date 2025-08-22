<!-- Sidebar -->
<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>" href="<?= base_url('admin/dashboard/') ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= str_contains($current_page, 'products') ? 'active' : '' ?>" href="<?= base_url('admin/products/') ?>">
                    <i class="bi bi-box-seam me-2"></i> Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page === 'orders.php' ? 'active' : '' ?>" href="<?= base_url('admin/orders.php') ?>">
                    <i class="bi bi-cart-check me-2"></i> Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page === 'index.php' && basename(dirname($_SERVER['PHP_SELF'])) === 'customers' ? 'active' : '' ?>" href="<?= base_url('admin/customers/') ?>">
                    <i class="bi bi-people me-2"></i> Customers
                </a>
            </li>
            <li class="nav-item">
    <a class="nav-link <?= $current_page === 'settings.php' ? 'active' : '' ?>" href="<?= base_url('admin/settings.php') ?>">
        <i class="bi bi-gear me-2"></i> Settings
    </a>
</li>
        </ul>
    </div>
</div>