<?php
$pageTitle = 'Your Shopping Cart';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/Cart.php';

$cartDetails = Cart::getCartDetails();
$cartItems = $cartDetails['items'];
$total = $cartDetails['total'];
?>

<style>
    /* Cart Page Styles */
    .cart-container {
        padding: 2rem 0;
    }
    
    .cart-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .cart-title {
        font-weight: 700;
        font-size: 1.8rem;
        color: #2d3748;
        position: relative;
        display: inline-block;
    }
    
    .cart-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 60px;
        height: 4px;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        border-radius: 2px;
    }
    
    /* Cart Item Styles */
    .cart-item {
        transition: all 0.3s ease;
        padding: 1rem 0;
    }
    
    .cart-item.updating {
        opacity: 0.7;
        pointer-events: none;
    }
    
    .cart-item img {
        border-radius: 0.5rem;
        max-height: 80px;
        width: auto;
        object-fit: contain;
    }
    
    .cart-item-name {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }
    
    .cart-item-category {
        font-size: 0.85rem;
        color: #718096;
    }
    
    .cart-item-stock {
        font-size: 0.8rem;
        color: #4a5568;
    }
    
    .quantity-control {
        width: 120px;
    }
    
    .quantity-control .btn {
        width: 36px;
        padding: 0.25rem;
    }
    
    .quantity-input {
        width: 40px;
        text-align: center;
        border-left: none;
        border-right: none;
    }
    
    .item-price {
        font-weight: 600;
        color: #2d3748;
    }
    
    .unit-price {
        font-size: 0.85rem;
        color: #718096;
    }
    
    .remove-item-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    
    /* Order Summary Styles */
    .order-summary-card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .order-summary-title {
        font-weight: 600;
        color: #2d3748;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    
    .summary-total {
        font-weight: 700;
        font-size: 1.1rem;
        color: #2d3748;
    }
    
    /* Buttons */
    .btn-clear-cart {
        background: #fff5f5;
        color: #e53e3e;
        border: 1px solid #fed7d7;
    }
    
    .btn-clear-cart:hover {
        background: #fee2e2;
    }
    
    .btn-checkout {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        border: none;
        padding: 0.75rem;
        font-weight: 500;
    }
    
    .btn-checkout:hover {
        background: linear-gradient(135deg, #5d7df4, #9a6bdb);
    }
    
    /* Empty Cart */
    .empty-cart {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .empty-cart-icon {
        font-size: 3.5rem;
        color: #cbd5e0;
        margin-bottom: 1.5rem;
    }
    
    .empty-cart-message {
        font-size: 1.1rem;
        color: #4a5568;
        margin-bottom: 1.5rem;
    }
    
    .continue-shopping-btn {
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
    
    .continue-shopping-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(110, 142, 251, 0.3);
        color: white;
    }
    
    /* Animations */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .pulse {
        animation: pulse 0.5s 2;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .cart-item {
            flex-direction: column;
        }
        
        .cart-item > div {
            margin-bottom: 1rem;
        }
        
        .cart-item img {
            max-height: 120px;
            margin-bottom: 1rem;
        }
    }
</style>

<main class="cart-container">
    <div class="container">
        <div class="cart-header">
            <h1 class="cart-title">Your Shopping Cart</h1>
        </div>
        
        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>Your cart is empty</h3>
                <p class="empty-cart-message">Looks like you haven't added anything to your cart yet</p>
                <a href="<?= base_url('index.php') ?>" class="continue-shopping-btn">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <?php foreach ($cartItems as $item): ?>
                                <?php $product = $item['product']; ?>
                                <div class="row align-items-center cart-item" data-product-id="<?= $product->id ?>">
                                    <div class="col-md-2">
                                        <a href="<?= base_url('product.php?id=' . $product->id) ?>">
                                            <?php if ($product->image_url): ?>
                                                <img src="<?= htmlspecialchars($product->image_url) ?>" class="img-fluid" alt="<?= htmlspecialchars($product->name) ?>">
                                            <?php else: ?>
                                                <div class="bg-light rounded p-3 text-center">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="<?= base_url('product.php?id=' . $product->id) ?>" class="text-decoration-none">
                                            <h5 class="cart-item-name"><?= htmlspecialchars($product->name) ?></h5>
                                        </a>
                                        <p class="cart-item-category"><?= htmlspecialchars($product->category) ?></p>
                                        <p class="cart-item-stock" data-stock="<?= $product->stock_quantity ?>">
                                            <?= $product->stock_quantity ?> available in stock
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group quantity-control">
                                            <button class="btn btn-outline-secondary quantity-minus">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="form-control quantity-input" 
                                                   value="<?= $item['quantity'] ?>" min="1" max="<?= $product->stock_quantity ?>">
                                            <button class="btn btn-outline-secondary quantity-plus">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <p class="mb-0 item-price">₵<?= number_format($product->price * $item['quantity'], 2) ?></p>
                                        <small class="unit-price">₵<?= number_format($product->price, 2) ?> each</small>
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button class="btn btn-sm remove-item-btn btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <hr class="my-3">
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card order-summary-card">
                        <div class="card-body">
                            <h5 class="order-summary-title">Order Summary</h5>
                            <div class="summary-row">
                                <span>Subtotal (<?= $cartDetails['count'] ?> items)</span>
                                <span>₵<?= number_format($total, 2) ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Delivery</span>
                                <span>Free</span>
                            </div>
                            <hr>
                            <div class="summary-row">
                                <span class="summary-total">Total</span>
                                <span class="summary-total">₵<?= number_format($total, 2) ?></span>
                            </div>
                            
                            <div class="d-grid gap-2 mt-4">
                                <button class="btn btn-clear-cart" id="clearCartBtn">
                                    <i class="fas fa-trash me-2"></i>Clear Cart
                                </button>
                                
                                <?php if (isLoggedIn()): ?>
                                    <a href="checkout.php" class="btn btn-checkout">
                                        <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                    </a>
                                <?php else: ?>
                                    <div class="alert alert-warning p-2 text-center">
                                        <a href="pages/auth/login.php?redirect=cart.php" class="btn btn-checkout w-100">
                                            <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Enhanced cart operations with stock notifications
function handleCartOperations() {
    // Clear cart functionality
    document.getElementById('clearCartBtn')?.addEventListener('click', async function() {
        if (!confirm('Are you sure you want to clear your entire cart?')) return;
        
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Clearing...';
        
        try {
            const response = await fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'clear_cart=1'
            });

            if (!response.ok) {
                throw new Error('Failed to clear cart. Please try again.');
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Failed to clear cart');
            }

            showToast('Success', data.message || 'Your cart has been cleared', 'success');
            setTimeout(() => location.reload(), 1000);
        } catch (error) {
            console.error('Error:', error);
            showToast('Error', error.message || 'An error occurred while clearing cart', 'danger');
            this.innerHTML = '<i class="fas fa-trash me-2"></i>Clear Cart';
        }
    });

    // Quantity change handlers
    document.querySelectorAll('.quantity-minus, .quantity-plus').forEach(button => {
        button.addEventListener('click', async function() {
            const row = this.closest('.cart-item');
            const input = row.querySelector('.quantity-input');
            let quantity = parseInt(input.value);
            
            if (this.classList.contains('quantity-minus')) {
                quantity = Math.max(1, quantity - 1);
            } else {
                quantity += 1;
            }
            
            input.value = quantity;
            await updateCartItem(row.dataset.productId, quantity, input, row);
        });
    });

    // Input change handler
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', async function() {
            const row = this.closest('.cart-item');
            let quantity = parseInt(this.value);
            
            if (isNaN(quantity)) quantity = 1;
            quantity = Math.max(1, quantity);
            
            this.value = quantity;
            await updateCartItem(row.dataset.productId, quantity, this, row);
        });
    });

    // Remove item handler
    document.querySelectorAll('.remove-item-btn').forEach(button => {
        button.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to remove this item?')) return;
            
            const row = this.closest('.cart-item');
            const productId = row.dataset.productId;
            
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            await updateCartItem(productId, 0, null, row, () => {
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    if (document.querySelectorAll('.cart-item').length === 0) {
                        location.reload();
                    }
                }, 300);
            });
        });
    });
}

// Enhanced update function
async function updateCartItem(productId, quantity, inputElement, rowElement, callback) {
    try {
        rowElement.classList.add('updating');
        
        const response = await fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&quantity=${quantity}`
        });

        if (!response.ok) {
            throw new Error(await response.text());
        }

        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Failed to update cart');
        }

        updateCartUI(data, productId, quantity, inputElement, rowElement);
        
        // Check stock limit
        const stockMessage = rowElement.querySelector('.cart-item-stock');
        if (stockMessage) {
            const availableStock = parseInt(stockMessage.dataset.stock);
            if (quantity >= availableStock) {
                showToast('Notice', `Only ${availableStock} available in stock`, 'warning');
            } else {
                showToast('Success', 'Cart updated', 'success');
            }
        }
        
        if (callback) callback();
    } catch (error) {
        console.error('Error:', error);
        let errorMessage = error.message;
        try {
            const errorData = JSON.parse(error.message);
            if (errorData.error) {
                errorMessage = errorData.error;
            }
        } catch (e) {}
        showToast('Error', errorMessage, 'danger');
        
        if (inputElement) {
            inputElement.value = inputElement.value; // Reset to current value
        }
    } finally {
        rowElement.classList.remove('updating');
        if (inputElement && inputElement.closest('.remove-item-btn')) {
            inputElement.closest('.remove-item-btn').innerHTML = '<i class="fas fa-trash"></i>';
        }
    }
}

// Update cart UI
function updateCartUI(data, productId, quantity, inputElement, rowElement) {
    // Update cart badges
    document.querySelectorAll('.cart-badge').forEach(badge => {
        badge.textContent = data.totalItems;
    });

    // Update Order Summary
    document.querySelector('.summary-row span:first-child').textContent = `Subtotal (${data.totalItems} items)`;
    document.querySelectorAll('.summary-row span:last-child').forEach((span, index) => {
        if (index === 0) span.textContent = `₵${data.totalPrice.toFixed(2)}`;
        if (index === 2) span.textContent = `₵${data.totalPrice.toFixed(2)}`;
    });

    // Update specific item if not deleted
    if (quantity > 0 && rowElement) {
        const unitPrice = parseFloat(rowElement.querySelector('.unit-price').textContent.replace('₵', ''));
        rowElement.querySelector('.item-price').textContent = '₵' + (unitPrice * quantity).toFixed(2);
        
        if (inputElement) {
            inputElement.value = quantity;
        }
    }
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

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', handleCartOperations);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>