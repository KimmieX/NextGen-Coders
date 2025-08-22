<?php
$pageTitle = 'Order Details';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/Order.php';

if (!isLoggedIn()) {
    redirect('/chantmo-supa/pages/auth/login.php');
}

$orderId = $_GET['id'] ?? 0;
$order = Order::getOrderWithItems($orderId, $_SESSION['user_id']);

if (!$order) {
    redirect('/chantmo-supa/orders.php');
}

// Status messages
$statusMessages = [
    'pending' => [
        'title' => 'Order Received',
        'message' => 'We\'ve received your order and will process it soon.',
        'icon' => 'fas fa-clock',
        'next_steps' => 'Please wait while we prepare your order.'
    ],
    'processing' => [
        'title' => 'Processing Order',
        'message' => 'Your order is being prepared.',
        'icon' => 'fas fa-cog',
        'next_steps' => 'We\'re getting your items ready.'
    ],
    'completed' => [
        'title' => 'Order Completed',
        'message' => 'Your order has been successfully processed.',
        'icon' => 'fas fa-check-circle',
        'next_steps' => 'Thank you for shopping with us!'
    ],
    'cancelled' => [
        'title' => 'Order Cancelled',
        'message' => 'This order has been cancelled.',
        'icon' => 'fas fa-times-circle',
        'next_steps' => 'Please contact us if you have any questions.'
    ]
];

$statusInfo = $statusMessages[$order['status']] ?? [
    'title' => 'Order Status',
    'message' => 'We\'re processing your order.',
    'icon' => 'fas fa-info-circle',
    'next_steps' => ''
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-bold mb-2">Order #<?= htmlspecialchars($order['order_number']) ?></h1>
                    <p class="text-muted">Placed on <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
                </div>
                <div>
                    <span class="badge bg-<?= 
                        $order['status'] === 'pending' ? 'warning' : 
                        ($order['status'] === 'processing' ? 'info' : 
                        ($order['status'] === 'completed' ? 'success' : 'danger'))
                    ?> rounded-pill px-3 py-2">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    <!-- Status Alert -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-<?= 
            $order['status'] === 'pending' ? 'warning' : 
            ($order['status'] === 'processing' ? 'info' : 
            ($order['status'] === 'completed' ? 'success' : 'danger'))
        ?> d-flex align-items-center">
            <div class="me-3">
                <i class="<?= $statusInfo['icon'] ?> fa-2x"></i>
            </div>
            <div>
                <h5 class="alert-heading mb-1"><?= $statusInfo['title'] ?></h5>
                <p class="mb-1"><?= $statusInfo['message'] ?></p>
                <?php if ($statusInfo['next_steps']): ?>
                    <p class="mb-0"><strong>Next Steps:</strong> <?= $statusInfo['next_steps'] ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <!-- Order Items -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Order Items</h5>
                    
                    <div class="list-group">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="list-group-item border-0 p-0 mb-3">
                                <div class="d-flex align-items-center">
                                    <?php if ($item['image_url']): ?>
                                    <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                         class="rounded me-3" 
                                         width="80" 
                                         height="80" 
                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                         style="object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                        <p class="text-muted small mb-1">Quantity: <?= $item['quantity'] ?></p>
                                        <p class="mb-0">
                                            <span class="fw-bold">₵<?= number_format($item['price'], 2) ?></span>
                                            <?php if ($item['original_price'] > $item['price']): ?>
                                                <span class="text-muted text-decoration-line-through ms-2">₵<?= number_format($item['original_price'], 2) ?></span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <p class="fw-bold mb-0">₵<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Order Summary</h5>
                    
                    <!-- In the Order Summary section -->
<div class="mb-4">
    <h6 class="fw-bold mb-3">Delivery Information</h6>
    <p class="mb-1"><?= nl2br(htmlspecialchars($order['address'] ?? 'Address not provided')) ?></p>
    <p class="mb-0">Phone: <?= htmlspecialchars($order['phone'] ?? 'Phone not provided') ?></p>
</div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Payment Method</h6>
                        <p class="mb-0">
                            <?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?>
                            (<?= $order['payment_status'] === 'paid' ? 'Paid' : 'Pending' ?>)
                        </p>
                    </div>
                    
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between fw-bold fs-5 mt-3">
                            <span>Total:</span>
                            <span>₵<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($order['status'] === 'pending'): ?>
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-3">Order Actions</h5>
                        <button class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                            <i class="fas fa-times me-2"></i> Request Cancellation
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Order Cancellation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to request cancellation for order #<?= htmlspecialchars($order['order_number']) ?>?</p>
                <form id="cancelOrderForm" method="POST" action="cancel-order.php">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">Reason for cancellation</label>
                        <textarea class="form-control" id="cancelReason" name="reason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="cancelOrderForm" class="btn btn-danger">Submit Request</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>