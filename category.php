<?php
$pageTitle = 'Category Products';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/Product.php';

if (!isset($_GET['name'])) {
    redirect('index.php');
}

$category = htmlspecialchars(trim($_GET['name']));
$products = Product::getByCategory($category);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4"><?= htmlspecialchars($category) ?></h1>
    
    <div class="row">
        <?php if (empty($products)): ?>
        <div class="col-12">
            <div class="alert alert-info">No products found in this category.</div>
        </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <?php if ($product->image_url): ?>
                    <img src="<?= htmlspecialchars($product->image_url) ?>" class="card-img-top" alt="<?= htmlspecialchars($product->name) ?>" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product->name) ?></h5>
                        <p class="card-text">
                            <strong>₵<?= number_format($product->price, 2) ?></strong>
                            <?php if ($product->hasDiscount()): ?>
                                <span class="text-muted text-decoration-line-through">₵<?= number_format($product->original_price, 2) ?></span>
                                <span class="badge bg-danger">Save <?= $product->getDiscountPercentage() ?>%</span>
                            <?php endif; ?>
                        </p>
                        <?php if ($product->badge): ?>
                            <span class="badge bg-info"><?= htmlspecialchars($product->badge) ?></span>
                        <?php endif; ?>
                        <?php if ($product->isOutOfStock()): ?>
                            <button class="btn btn-secondary w-100 mt-2" disabled>Out of Stock</button>
                        <?php else: ?>
                            <button class="btn btn-primary w-100 mt-2">Add to Cart</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>