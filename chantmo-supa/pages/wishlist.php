<?php
$pageTitle = 'My Wishlist';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/Product.php';

if (!isLoggedIn()) {
    redirect('/chantmo-supa/pages/auth/login.php?redirect=wishlist.php');
}

$wishlistItems = Product::getWishlistItems($_SESSION['user_id']);

require_once __DIR__ . '/../includes/header.php';
?>

<style>
    /* Wishlist specific styles */
    .wishlist-container {
        padding: 2rem 0;
    }
    
    .wishlist-header {
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .wishlist-title {
        font-weight: 700;
        font-size: 1.8rem;
        color: #2d3748;
        margin: 0;
    }
    
    /* Gradient wishlist count */
    .wishlist-count {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        color: white;
        font-size: 1rem;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        margin-left: 0.75rem;
    }
    
    /* Simplified product card for wishlist */
    .wishlist-product-card {
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        height: 100%;
    }
    
    .wishlist-product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .wishlist-product-card .product-image-container {
        position: relative;
        padding-top: 70%;
        overflow: hidden;
    }
    
    .wishlist-product-card .product-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .wishlist-product-card:hover .product-image {
        transform: scale(1.05);
    }
    
    .wishlist-product-card .product-details {
        padding: 1rem;
    }
    
    .wishlist-product-card .product-name {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    
    .wishlist-product-card .product-name a {
        color: inherit;
        text-decoration: none;
    }
    
    .wishlist-product-card .product-price {
        margin-bottom: 0.5rem;
    }
    
    .wishlist-product-card .current-price {
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    .wishlist-product-card .original-price {
        font-size: 0.75rem;
    }
    
    .wishlist-product-card .btn-add-to-cart,
    .wishlist-product-card .btn-wishlist {
        padding: 0.35rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 4px;
    }
    
    .wishlist-product-card .btn-add-to-cart {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        border: none;
        color: white;
    }
    
    .wishlist-product-card .btn-wishlist {
        background: #fff5f5;
        border: 1px solid #ff6b6b;
        color: #ff6b6b;
        width: 36px;
    }
    
    .wishlist-product-card .product-actions {
        position: absolute;
        bottom: 10px;
        right: 10px;
    }
    
    .wishlist-product-card .quick-view-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: white;
        border: none;
        color: #2d3748;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .wishlist-product-card .quick-view-btn:hover {
        background: #6e8efb;
        color: white;
    }
    
    /* Empty wishlist message */
    .empty-wishlist {
        background: white;
        border-radius: 0.75rem;
        padding: 3rem 2rem;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin: 2rem 0;
    }
    
    .empty-wishlist-icon {
        font-size: 3.5rem;
        background: linear-gradient(135deg, #f5f7fa, #e6ebf5);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin-bottom: 1.5rem;
        display: inline-block;
    }
    
    .empty-wishlist h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: #2d3748;
    }
    
    .empty-wishlist p {
        color: #718096;
        margin-bottom: 1.5rem;
    }
    
    .browse-link {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        color: white;
        padding: 0.6rem 1.75rem;
        border-radius: 50px;
        display: inline-block;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(110, 142, 251, 0.2);
    }
    
    .browse-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(110, 142, 251, 0.3);
        color: white;
    }
</style>

<main class="wishlist-container">
    <div class="container">
        <div class="wishlist-header">
            <h1 class="wishlist-title">My Wishlist <span class="wishlist-count"><?= count($wishlistItems) ?></span></h1>
            <?php if (!empty($wishlistItems)): ?>
                <a href="<?= base_url('index.php') ?>" class="btn btn-outline-primary">Continue Shopping</a>
            <?php endif; ?>
        </div>
        
        <?php if (empty($wishlistItems)): ?>
            <div class="empty-wishlist">
                <div class="empty-wishlist-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h3>Your wishlist is empty</h3>
                <p>You haven't added any items to your wishlist yet</p>
                <a href="<?= base_url('index.php') ?>" class="browse-link">Browse Products</a>
            </div>
        <?php else: ?>
            <div class="row g-3" id="wishlistContainer">
                <?php foreach ($wishlistItems as $product): ?>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                        <?php include __DIR__ . '/../includes/wishlist-product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle add to cart
    document.addEventListener('click', function(e) {
        const addToCartBtn = e.target.closest('.btn-add-to-cart');
        if (addToCartBtn && !addToCartBtn.disabled) {
            e.preventDefault();
            const productId = addToCartBtn.getAttribute('data-product-id');
            
            // Disable button during request
            addToCartBtn.disabled = true;
            const originalText = addToCartBtn.innerHTML;
            addToCartBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            // Send AJAX request
            fetch('/chantmo-supa/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count in header
                    document.querySelectorAll('.cart-badge').forEach(badge => {
                        badge.textContent = data.totalItems;
                    });
                    
                    // Show success toast
                    showToast('Success', 'Product added to cart!', 'success');
                } else {
                    showToast('Error', data.error || 'Failed to add to cart', 'danger');
                }
            })
            .catch(error => {
                showToast('Error', 'Failed to add to cart', 'danger');
            })
            .finally(() => {
                addToCartBtn.disabled = false;
                addToCartBtn.innerHTML = originalText;
            });
        }
    });

    // Handle wishlist removal
    document.addEventListener('click', function(e) {
        const wishlistBtn = e.target.closest('.btn-wishlist');
        if (wishlistBtn && wishlistBtn.classList.contains('active')) {
            e.preventDefault();
            const productId = wishlistBtn.getAttribute('data-product-id');
            
            // Disable button during request
            wishlistBtn.disabled = true;
            const originalHTML = wishlistBtn.innerHTML;
            wishlistBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            // Send AJAX request
            fetch('/chantmo-supa/wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&action=remove`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the product card immediately
                    const productCard = document.querySelector(`.wishlist-product-card[data-id="${productId}"]`);
                    if (productCard) {
                        productCard.style.transition = 'all 0.3s ease';
                        productCard.style.opacity = '0';
                        productCard.style.transform = 'scale(0.9)';
                        
                        setTimeout(() => {
                            productCard.remove();
                            
                            // Update wishlist count
                            document.querySelectorAll('.wishlist-count').forEach(el => {
                                el.textContent = data.wishlistCount;
                            });
                            
                            // Show empty message if no items left
                            if (document.querySelectorAll('#wishlistContainer .wishlist-product-card').length === 0) {
                                document.querySelector('#wishlistContainer').innerHTML = `
                                    <div class="empty-wishlist">
                                        <div class="empty-wishlist-icon">
                                            <i class="fas fa-heart"></i>
                                        </div>
                                        <h3>Your wishlist is empty</h3>
                                        <p>You haven't added any items to your wishlist yet</p>
                                        <a href="<?= base_url('index.php') ?>" class="browse-link">Browse Products</a>
                                    </div>
                                `;
                            }
                        }, 300);
                    }
                    
                    showToast('Success', data.message, 'success');
                } else {
                    showToast('Error', data.error || 'Failed to update wishlist', 'danger');
                    wishlistBtn.innerHTML = originalHTML;
                    wishlistBtn.disabled = false;
                }
            })
            .catch(error => {
                showToast('Error', 'Failed to update wishlist', 'danger');
                wishlistBtn.innerHTML = originalHTML;
                wishlistBtn.disabled = false;
            });
        }
    });

    // Toast notification function
    function showToast(title, message, type) {
        const toastContainer = document.createElement('div');
        toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
        toastContainer.style.zIndex = '1090';
        
        toastContainer.innerHTML = `
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-${type} text-white">
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        
        document.body.appendChild(toastContainer);
        
        setTimeout(() => {
            toastContainer.remove();
        }, 3000);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>