<?php
// MUST be first - no whitespace before!
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Product.php';
require_once __DIR__ . '/includes/functions.php';

// Handle product not found redirects
if (isset($_SESSION['product_error'])) {
    $error = $_SESSION['product_error'];
    unset($_SESSION['product_error']);
    
    // Store for display after header loads
    $displayError = $error;
}

$pageTitle = 'All Products';
require_once __DIR__ . '/includes/header.php';

// Display errors if they exist
if (isset($displayError)): ?>
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($displayError) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif;

// Pagination setup
$perPage = 12;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Get products with error handling
try {
    $products = Product::getAll($perPage, $offset);
    $totalProducts = Product::count();
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $products = [];
    $totalProducts = 0;
    echo '<div class="alert alert-warning">Temporarily unable to load products. Please try again later.</div>';
}
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Our Products</h1>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Sort By
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?sort=newest">Newest First</a></li>
                <li><a class="dropdown-item" href="?sort=price_asc">Price: Low to High</a></li>
                <li><a class="dropdown-item" href="?sort=price_desc">Price: High to Low</a></li>
            </ul>
        </div>
    </div>
    
    <?php if (empty($products)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No products available at this time.
            <a href="<?= base_url() ?>" class="alert-link">Browse our homepage</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <?php include __DIR__ . '/includes/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($totalProducts > $perPage): ?>
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page-1 ?>">Previous</a>
                    </li>
                    
                    <?php 
                    $startPage = max(1, $page - 2);
                    $endPage = min(ceil($totalProducts/$perPage), $page + 2);
                    
                    for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?= $page >= ceil($totalProducts/$perPage) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page+1 ?>">Next</a>
                    </li>
                </ul>
                <div class="text-center text-muted mt-2">
                    Page <?= $page ?> of <?= ceil($totalProducts/$perPage) ?> | 
                    Showing <?= count($products) ?> of <?= $totalProducts ?> products
                </div>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Recently Viewed Section -->
<?php 
if (!empty($_SESSION['recently_viewed'])):
    $recentlyViewed = [];
    try {
        $placeholders = implode(',', array_fill(0, count($_SESSION['recently_viewed']), '?'));
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND id NOT IN (SELECT id FROM products WHERE id IS NULL) ORDER BY FIELD(id, " . implode(',', $_SESSION['recently_viewed']) . ")");
        $stmt->execute($_SESSION['recently_viewed']);
        $recentlyViewed = $stmt->fetchAll(PDO::FETCH_CLASS, 'Product');
    } catch (PDOException $e) {
        error_log("Recently viewed query failed: " . $e->getMessage());
    }
    
    if (!empty($recentlyViewed)): ?>
        <section class="py-5 bg-light">
            <div class="container">
                <h2 class="fw-bold mb-4">Recently Viewed</h2>
                <div class="row g-4">
                    <?php foreach ($recentlyViewed as $product): ?>
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <?php include __DIR__ . '/includes/product-card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif;
endif; 
?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>