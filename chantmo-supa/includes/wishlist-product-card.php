<div class="wishlist-product-card" data-id="<?= $product->id ?>">
    <div class="product-badges">
        <?php if ($product->badge): ?>
            <span class="product-badge"><?= htmlspecialchars($product->badge) ?></span>
        <?php endif; ?>
        <?php if ($product->hasDiscount()): ?>
            <span class="discount-badge"><?= $product->getDiscountPercentage() ?>% OFF</span>
        <?php endif; ?>
    </div>
    
    <a href="<?= base_url('product.php?id=' . $product->id) ?>" class="product-image-container">
        <?php if ($product->image_url): ?>
            <img src="<?= htmlspecialchars($product->image_url) ?>" class="product-image" alt="<?= htmlspecialchars($product->name) ?>">
        <?php else: ?>
            <div class="product-image-placeholder">
                <i class="fas fa-image"></i>
            </div>
        <?php endif; ?>
    </a>
    
    <div class="product-details">
        <h3 class="product-name">
            <a href="<?= base_url('product.php?id=' . $product->id) ?>">
                <?= htmlspecialchars($product->name) ?>
            </a>
        </h3>
        
        <div class="product-price">
            <span class="current-price">₵<?= number_format($product->price, 2) ?></span>
            <?php if ($product->hasDiscount()): ?>
                <span class="original-price">₵<?= number_format($product->original_price, 2) ?></span>
            <?php endif; ?>
        </div>
        
        <div class="d-flex gap-2 mt-2">
            <button class="btn btn-add-to-cart flex-grow-1 <?= $product->isOutOfStock() ? 'disabled' : '' ?>" 
                    data-product-id="<?= $product->id ?>"
                    <?= $product->isOutOfStock() ? 'disabled' : '' ?>>
                <?= $product->isOutOfStock() ? 'Out of Stock' : 'Add to Cart' ?>
            </button>
            <button class="btn btn-wishlist active" data-product-id="<?= $product->id ?>">
                <i class="fas fa-heart"></i>
            </button>
        </div>
    </div>
</div>