<div class="product-card" data-id="<?= $product->id ?>">
    <div class="product-badges">
        <?php if ($product->badge): ?>
            <span class="product-badge"><?= htmlspecialchars($product->badge) ?></span>
        <?php endif; ?>
        <?php if ($product->hasDiscount()): ?>
            <span class="discount-badge"><?= $product->getDiscountPercentage() ?>% OFF</span>
        <?php endif; ?>
    </div>
    
    <div class="product-image-container">
         <a href="<?= base_url('product.php?id=' . $product->id) ?>">
            <?php if ($product->image_url): ?>
                <img src="<?= htmlspecialchars($product->image_url) ?>" class="product-image" alt="<?= htmlspecialchars($product->name) ?>">
            <?php else: ?>
                <div class="product-image-placeholder">
                    <i class="fas fa-image"></i>
                </div>
            <?php endif; ?>
        </a>
        <?php if ($product->image_url): ?>
            <img src="<?= htmlspecialchars($product->image_url) ?>" class="product-image" alt="<?= htmlspecialchars($product->name) ?>">
        <?php else: ?>
            <div class="product-image-placeholder">
                <i class="fas fa-image"></i>
            </div>
        <?php endif; ?>
        
        <div class="product-actions">
            <button class="quick-view-btn" data-bs-toggle="modal" data-bs-target="#quickViewModal" 
                data-product-id="<?= $product->id ?>"
                data-product-name="<?= htmlspecialchars($product->name) ?>"
                data-product-image="<?= htmlspecialchars($product->image_url) ?>"
                data-current-price="₵<?= number_format($product->price, 2) ?>"
                data-original-price="<?= $product->hasDiscount() ? '₵'.number_format($product->original_price, 2) : '' ?>"
                data-discount="<?= $product->hasDiscount() ? $product->getDiscountPercentage().'%' : '' ?>"
                data-description="<?= htmlspecialchars($product->description) ?>"
                data-category="<?= htmlspecialchars($product->category) ?>"
                data-stock="<?= $product->stock_quantity ?>"
                data-expiry="<?= $product->expiry_date ? date('M d, Y', strtotime($product->expiry_date)) : 'N/A' ?>">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>
    
    <div class="product-details">
        <h3 class="product-name"><?= htmlspecialchars($product->name) ?></h3>
        
        <div class="product-price">
            <span class="current-price">₵<?= number_format($product->price, 2) ?></span>
            <?php if ($product->hasDiscount()): ?>
                <span class="original-price">₵<?= number_format($product->original_price, 2) ?></span>
            <?php endif; ?>
        </div>
        
        <div class="d-flex gap-2">
            <button class="btn btn-add-to-cart flex-grow-1 <?= $product->isOutOfStock() ? 'disabled' : '' ?>" 
                    data-product-id="<?= $product->id ?>"
                    <?= $product->isOutOfStock() ? 'disabled' : '' ?>>
                <?= $product->isOutOfStock() ? 'Out of Stock' : 'Add to Cart' ?>
            </button>
            <button class="btn btn-wishlist <?= isLoggedIn() && Product::isInWishlist($_SESSION['user_id'] ?? 0, $product->id) ? 'active' : '' ?>" 
    data-product-id="<?= $product->id ?>">
    <i class="fas fa-heart"></i>
    <span class="wishlist-text">
        <?= isLoggedIn() && Product::isInWishlist($_SESSION['user_id'] ?? 0, $product->id) ? 'In Wishlist' : 'Add to Wishlist' ?>
    </span>
</button>
        </div>
    </div>
</div>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img src="" class="img-fluid rounded mb-3" id="qvProductImage" alt="">
                    </div>
                    <div class="col-md-6">
                        <h3 id="qvProductName"></h3>
                        <div class="price-container mb-3">
                            <span class="current-price h4" id="qvProductPrice"></span>
                            <span class="original-price text-muted text-decoration-line-through" id="qvOriginalPrice"></span>
                            <span class="discount-badge badge bg-danger ms-2" id="qvDiscountBadge"></span>
                        </div>
                        <div class="badges mb-3" id="qvBadges"></div>
                        <p class="product-description" id="qvDescription"></p>
                        
                        
                        <div class="d-flex gap-2">
                            <button class="btn btn-gradient add-to-cart" 
                                    id="qvAddToCart"
                                    data-product-id="">
                                Add to Cart
                            </button>
                            <button class="btn btn-outline-secondary btn-wishlist" 
                                    id="qvWishlistBtn"
                                    data-product-id="">
                                <i class="fas fa-heart"></i> Wishlist
                            </button>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="product-meta">
                            <p><strong>Category:</strong> <span id="qvCategory"></span></p>
                            <p><strong>Stock:</strong> <span id="qvStock"></span></p>
                            <p><strong>Expiry:</strong> <span id="qvExpiry"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>