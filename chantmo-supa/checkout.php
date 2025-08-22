<?php
$pageTitle = 'Checkout';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/Cart.php';

if (!isLoggedIn()) {
    redirect('/chantmo-supa/pages/auth/login.php?redirect=checkout.php');
}

$cartDetails = Cart::getCartDetails();
$cartItems = $cartDetails['items'];

if (empty($cartItems)) {
    redirect('/chantmo-supa/index.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    if (empty($address) || empty($phone) || !in_array($paymentMethod, ['cash', 'mobile_money'])) {
        $_SESSION['error'] = 'Please fill all required fields with valid data';
    } else {
        try {
            $pdo->beginTransaction();

            // Create order
            $orderNumber = 'ORD-' . strtoupper(uniqid());
            $stmt = $pdo->prepare("
                INSERT INTO orders 
                (user_id, order_number, total_amount, payment_method, address, phone, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $orderNumber,
                $cartDetails['total'],
                $paymentMethod,
                $address,
                $phone,
                $notes
            ]);
            $orderId = $pdo->lastInsertId();

            // Add order items
            foreach ($cartItems as $item) {
                $stmt = $pdo->prepare("
                    INSERT INTO order_items 
                    (order_id, product_id, quantity, price) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $orderId,
                    $item['product']->id,
                    $item['quantity'],
                    $item['product']->price
                ]);

                // Update product stock
                $stmt = $pdo->prepare("
                    UPDATE products 
                    SET stock_quantity = stock_quantity - ? 
                    WHERE id = ?
                ");
                $stmt->execute([$item['quantity'], $item['product']->id]);
            }

            $stmt = $pdo->prepare("UPDATE users SET address = ?, phone = ? WHERE id = ? AND (address IS NULL OR phone IS NULL)");
            $stmt->execute([$address, $phone, $_SESSION['user_id']]);

            $pdo->commit();
            
            // Clear cart
            Cart::clear();
            
            // Redirect to order confirmation
            redirect("/chantmo-supa/order-confirmation.php?id=$orderId");
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Error processing your order. Please try again.';
            error_log("Order processing error: " . $e->getMessage());
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* Checkout Page Styles */
    .checkout-container {
        padding: 2rem 0;
    }
    
    .checkout-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .checkout-title {
        font-weight: 700;
        font-size: 1.8rem;
        color: #2d3748;
        position: relative;
        display: inline-block;
    }
    
    .checkout-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 60px;
        height: 4px;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        border-radius: 2px;
    }
    
    /* Form Styles */
    .checkout-card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .form-label {
        font-weight: 500;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }
    
    .form-control, .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #a777e3;
        box-shadow: 0 0 0 0.25rem rgba(167, 119, 227, 0.25);
    }
    
    .form-check-input {
        width: 1.2em;
        height: 1.2em;
        margin-top: 0.2em;
    }
    
    .form-check-input:checked {
        background-color: #6e8efb;
        border-color: #6e8efb;
    }
    
    .form-check-label {
        margin-left: 0.5rem;
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
    
    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 1rem 0;
    }
    
    .list-group-item:first-child {
        border-top: none;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
    
    /* Buttons */
    .btn-checkout {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        border: none;
        padding: 0.75rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .btn-checkout:hover {
        background: linear-gradient(135deg, #5d7df4, #9a6bdb);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(110, 142, 251, 0.3);
    }
    
    /* Payment Method Styles */
    .payment-method {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        margin-bottom: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .payment-method:hover {
        border-color: #a777e3;
    }
    
    .payment-method.active {
        border-color: #6e8efb;
        background-color: rgba(110, 142, 251, 0.05);
    }
    
    .payment-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border-radius: 50%;
        margin-right: 1rem;
        color: #6e8efb;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .checkout-title {
            font-size: 1.5rem;
        }
        
        .checkout-card, .order-summary-card {
            margin-bottom: 1.5rem;
        }
    }
</style>

<main class="checkout-container">
    <div class="container">
        <div class="checkout-header">
            <h1 class="checkout-title">Checkout</h1>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card checkout-card mb-4">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Shipping & Payment</h2>
                        
                        <?php displayMessage(); ?>
                        
                        <form method="POST" id="checkoutForm">
                            <div class="mb-4">
                                <label for="address" class="form-label">Delivery Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label mb-3">Payment Method</label>
                                
                                <div class="payment-method active" onclick="document.getElementById('cash').checked = true; this.classList.add('active'); document.getElementById('mobile_money_method').classList.remove('active');">
                                    <div class="payment-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Cash on Delivery</h6>
                                        <small class="text-muted">Pay when you receive your order</small>
                                    </div>
                                    <input type="radio" class="form-check-input d-none" name="payment_method" 
                                           id="cash" value="cash" checked>
                                </div>
                                
                                <div class="payment-method" id="mobile_money_method" onclick="document.getElementById('mobile_money').checked = true; this.classList.add('active'); document.getElementById('cash_method').classList.remove('active');">
                                    <div class="payment-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Mobile Money</h6>
                                        <small class="text-muted">MTN/Vodafone mobile payments</small>
                                    </div>
                                    <input type="radio" class="form-check-input d-none" name="payment_method" 
                                           id="mobile_money" value="mobile_money">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="notes" class="form-label">Order Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                                <small class="text-muted">Special instructions for your order</small>
                            </div>
                            
                            <button type="submit" class="btn btn-checkout w-100 py-3">
                                <i class="fas fa-credit-card me-2"></i> Place Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card order-summary-card">
                    <div class="card-body">
                        <h5 class="order-summary-title mb-4">Order Summary</h5>
                        
                        <div class="list-group mb-3">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <?php if ($item['product']->image_url): ?>
                                                <img src="<?= htmlspecialchars($item['product']->image_url) ?>" 
                                                     class="img-thumbnail me-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;" 
                                                     alt="<?= htmlspecialchars($item['product']->name) ?>">
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($item['product']->name) ?></h6>
                                                <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span>₵<?= number_format($item['product']->price * $item['quantity'], 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>₵<?= number_format($cartDetails['total'], 2) ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery</span>
                            <span>Free</span>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total</span>
                            <span>₵<?= number_format($cartDetails['total'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle payment method selection
    const paymentMethods = document.querySelectorAll('.payment-method');
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            paymentMethods.forEach(m => m.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Form submission handling
    const form = document.getElementById('checkoutForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing Order...';
            
            // You could add additional validation here if needed
            
            // Form will submit normally if all validations pass
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>