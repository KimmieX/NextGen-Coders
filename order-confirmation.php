<?php
$pageTitle = 'Order Confirmation';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn()) {
    redirect('/chantmo-supa/pages/auth/login.php');
}

$orderId = $_GET['id'] ?? 0;

// Get order details
$stmt = $pdo->prepare("
    SELECT o.*, u.username 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    redirect('/chantmo-supa/pages/orders.php');
}

require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* Order Confirmation Styles */
    .confirmation-container {
        padding: 4rem 0;
    }
    
    .confirmation-card {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        background: white;
    }
    
    .confirmation-icon {
        font-size: 5rem;
        color: #28a745;
        margin-bottom: 1.5rem;
        position: relative;
    }
    
    .confirmation-icon::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        border-radius: 2px;
    }
    
    .confirmation-title {
        font-weight: 700;
        font-size: 2.2rem;
        color: #2d3748;
        margin-bottom: 1rem;
    }
    
    .confirmation-subtitle {
        font-size: 1.2rem;
        color: #4a5568;
        margin-bottom: 2rem;
    }
    
    .order-details-card {
        background: #f8f9fa;
        border: none;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .order-details-title {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
        position: relative;
        display: inline-block;
    }
    
    .order-details-title::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 40px;
        height: 3px;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        border-radius: 2px;
    }
    
    .order-detail {
        margin-bottom: 0.5rem;
        color: #4a5568;
    }
    
    .order-detail strong {
        color: #2d3748;
    }
    
    .confirmation-message {
        color: #4a5568;
        margin-bottom: 2rem;
        font-size: 1.1rem;
    }
    
    .confirmation-btn {
        padding: 0.75rem 1.75rem;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .btn-outline-primary {
        border: 2px solid transparent;
        background: linear-gradient(white, white) padding-box,
                    linear-gradient(135deg, #6e8efb, #a777e3) border-box;
        color: #2d3748;
    }
    
    .btn-outline-primary:hover {
        background: linear-gradient(white, white) padding-box,
                    linear-gradient(135deg, #5d7df4, #9a6bdb) border-box;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        border: none;
        color: white;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5d7df4, #9a6bdb);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        color: white;
    }
    
    .status-badge {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-weight: 500;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .confirmation-title {
            font-size: 1.8rem;
        }
        
        .confirmation-subtitle {
            font-size: 1rem;
        }
        
        .confirmation-icon {
            font-size: 4rem;
        }
    }
</style>

<main class="confirmation-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="confirmation-card">
                    <div class="card-body py-5 px-4 px-md-5 text-center">
                        <div class="confirmation-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h1 class="confirmation-title">Order Confirmed!</h1>
                        <p class="confirmation-subtitle">Thank you for your order, <?= htmlspecialchars($order['username']) ?>!</p>
                        
                        <div class="order-details-card text-center">
                            <h5 class="order-details-title">Order #<?= $order['order_number'] ?></h5>
                            <p class="order-detail"><strong>Total:</strong> ₵<?= number_format($order['total_amount'], 2) ?></p>
                            <p class="order-detail"><strong>Payment Method:</strong> <?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></p>
                            <p class="order-detail mb-0"><strong>Status:</strong> <span class="status-badge"><?= ucfirst($order['status']) ?></span></p>
                        </div>
                        
                        <p class="confirmation-message">We've received your order and will process it shortly. You'll receive a confirmation email with your order details.</p>
                        
                        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                            <a href="/chantmo-supa/index.php" class="btn btn-outline-primary confirmation-btn">
                                <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                            </a>
                            <a href="/chantmo-supa/orders.php" class="btn btn-primary confirmation-btn">
                                <i class="fas fa-clipboard-list me-2"></i>View My Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>