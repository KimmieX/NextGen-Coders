// Add this at the top of your main.js file
function base_url(path = '') {
    return window.BASE_PATH + '/' + path.replace(/^\//, '');
}

function asset_url(path = '') {
    return window.BASE_PATH + '/assets/' + path.replace(/^\//, '');
}


document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    let addToCartInitialized = false;

    // Initialize Bootstrap components
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });


    // Back to Top Button
    const setupBackToTop = () => {
        const backToTop = document.getElementById('backToTop');
        if (backToTop) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTop.classList.add('show');
                } else {
                    backToTop.classList.remove('show');
                }
            });
            
            backToTop.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    };

    // Smooth Scrolling
    const setupSmoothScrolling = () => {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                if (this.getAttribute('href') === '#') return;
                
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
    };

    // Quick View Modal
    const setupQuickView = () => {
        const quickViewModal = document.getElementById('quickViewModal');
        if (!quickViewModal) return;

        quickViewModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            
            // Populate modal
            document.getElementById('qvProductName').textContent = button.getAttribute('data-product-name');
            document.getElementById('qvProductImage').src = button.getAttribute('data-product-image') || '';
            document.getElementById('qvProductPrice').textContent = button.getAttribute('data-current-price');
            document.getElementById('qvOriginalPrice').textContent = button.getAttribute('data-original-price');
            document.getElementById('qvDiscountBadge').textContent = button.getAttribute('data-discount');
            document.getElementById('qvDescription').textContent = button.getAttribute('data-description') || 'No description available';
            document.getElementById('qvCategory').textContent = button.getAttribute('data-category');
            document.getElementById('qvStock').textContent = `${button.getAttribute('data-stock')} in stock`;
            document.getElementById('qvExpiry').textContent = button.getAttribute('data-expiry');
            
            // Set up buttons
            const addToCartBtn = document.getElementById('qvAddToCart');
            const wishlistBtn = document.getElementById('qvWishlistBtn');
            
            addToCartBtn.setAttribute('data-product-id', productId);
            wishlistBtn.setAttribute('data-product-id', productId);
            
            // Check wishlist status
            checkWishlistStatus(productId, wishlistBtn);
        });
    };

    // Add to Cart Functionality
    const setupAddToCart = () => {
        if (addToCartInitialized) return;
        addToCartInitialized = true;
        
        document.addEventListener('click', async function(e) {
            const button = e.target.closest('.btn-add-to-cart, .add-to-cart');
            if (!button || button.classList.contains('disabled') || button.disabled) return;
            
            e.preventDefault();
            e.stopPropagation();
            
            const productId = button.getAttribute('data-product-id');
            if (!productId) return;

            // Disable all add to cart buttons
            document.querySelectorAll('.btn-add-to-cart, .add-to-cart').forEach(btn => {
                btn.disabled = true;
            });
            
            // Visual feedback
            const originalHTML = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

            try {
                const response = await fetch('/chantmo-supa/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=1`
                });

                if (!response.ok) throw new Error(await response.text());
                
                const data = await response.json();
                if (!data.success) throw new Error(data.error || 'Failed to add to cart');
                
                // Update cart count
                document.querySelectorAll('.cart-badge').forEach(badge => {
                    badge.textContent = data.totalItems;
                });
                
                showToast('Success', 'Product added to cart!', 'success');
                
            } catch (error) {
                console.error('Error:', error);
                showToast('Error', error.message || 'Failed to add to cart', 'danger');
            } finally {
                // Re-enable buttons and restore original state
                document.querySelectorAll('.btn-add-to-cart, .add-to-cart').forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                });
            }
        });
    };

    // Wishlist Functionality
const setupWishlist = () => {
    document.addEventListener('click', async function(e) {
        const wishlistBtn = e.target.closest('.btn-wishlist');
        if (!wishlistBtn) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        if (!isLoggedIn()) {
            showToast('Login Required', 'Please login to manage your wishlist', 'warning');
            return;
        }
        
        const productId = wishlistBtn.getAttribute('data-product-id');
        const isActive = wishlistBtn.classList.contains('active');
        const action = isActive ? 'remove' : 'add';
        
        // Disable all wishlist buttons for this product
        document.querySelectorAll(`.btn-wishlist[data-product-id="${productId}"]`).forEach(btn => {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
        });

        try {
            const response = await fetch('/chantmo-supa/wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&action=${action}`
            });

            if (!response.ok) throw new Error(await response.text());
            
            const data = await response.json();
            if (!data.success) throw new Error(data.error || 'Failed to update wishlist');
            
            // Update all wishlist buttons for this product
            document.querySelectorAll(`.btn-wishlist[data-product-id="${productId}"]`).forEach(btn => {
                btn.classList.toggle('active', data.inWishlist);
                btn.innerHTML = data.inWishlist 
                    ? '<i class="fas fa-heart"></i> In Wishlist' 
                    : '<i class="fas fa-heart"></i> Add to Wishlist';
                btn.disabled = false;
            });
            
            // Update all wishlist count badges
            document.querySelectorAll('.wishlist-count').forEach(el => {
                el.textContent = data.wishlistCount;
            });
            
            // If on wishlist page and item was removed, remove it from view
            if (!data.inWishlist && window.location.pathname.includes('wishlist.php')) {
                const productCard = document.querySelector(`.product-card[data-id="${productId}"]`);
                if (productCard) {
                    productCard.remove();
                    
                    // Show empty message if no items left
                    if (document.querySelectorAll('#wishlistContainer .product-card').length === 0) {
                        document.querySelector('#wishlistContainer').innerHTML = `
                            <div class="alert alert-info">
                                Your wishlist is empty. <a href="<?= base_url('index.php') ?>">Browse products</a>
                            </div>
                        `;
                    }
                }
            }
            
            showToast('Success', data.message, 'success');
            
        } catch (error) {
            console.error('Error:', error);
            showToast('Error', error.message || 'Failed to update wishlist', 'danger');
            
            // Revert buttons to original state
            document.querySelectorAll(`.btn-wishlist[data-product-id="${productId}"]`).forEach(btn => {
                btn.innerHTML = isActive 
                    ? '<i class="fas fa-heart"></i> In Wishlist' 
                    : '<i class="fas fa-heart"></i> Add to Wishlist';
                btn.disabled = false;
            });
        }
    });
};

// Helper function to check wishlist status
async function checkWishlistStatus(productId, button) {
    if (!isLoggedIn()) return;
    
    try {
        const response = await fetch(`/chantmo-supa/wishlist.php?product_id=${productId}&check_status=1`);
        const data = await response.json();
        
        if (data.success) {
            button.classList.toggle('active', data.inWishlist);
            button.innerHTML = data.inWishlist 
                ? '<i class="fas fa-heart"></i> In Wishlist' 
                : '<i class="fas fa-heart"></i> Add to Wishlist';
        }
    } catch (error) {
        console.error('Error checking wishlist status:', error);
    }
}
    

    // Helper function to check wishlist status
    async function checkWishlistStatus(productId, button) {
        if (!isLoggedIn()) return;
        
        try {
            const response = await fetch(`/chantmo-supa/wishlist.php?product_id=${productId}&check_status=1`);
            const data = await response.json();
            
            if (data.success) {
                button.classList.toggle('active', data.inWishlist);
                button.innerHTML = data.inWishlist 
                    ? '<i class="fas fa-heart"></i> In Wishlist' 
                    : '<i class="fas fa-heart"></i> Add to Wishlist';
            }
        } catch (error) {
            console.error('Error checking wishlist status:', error);
        }
    }

    // Helper function to check login status
    function isLoggedIn() {
        return document.body.classList.contains('logged-in');
    }

    // Helper function to get current cart quantity
    function getCurrentCartQuantity(productId) {
        const cartItems = window.cartItems || {};
        return cartItems[productId] || 0;
    }

    // Toast Notification
    function showToast(title, message, type = 'success') {
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


    // Your other initialization functions
    setupBackToTop();
    setupSmoothScrolling();
    setupQuickView();
    setupAddToCart();
    setupWishlist();
});