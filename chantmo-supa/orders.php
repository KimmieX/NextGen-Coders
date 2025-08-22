<?php
$pageTitle = 'My Orders';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/Order.php';

if (!isLoggedIn()) {
    redirect('/chantmo-supa/pages/auth/login.php');
}

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Get orders with pagination
$orders = Order::getUserOrders($_SESSION['user_id'], $perPage, $offset);
$totalOrders = Order::count(['user_id' => $_SESSION['user_id']]);
$totalPages = ceil($totalOrders / $perPage);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-bold mb-2">My Orders</h1>
                    <p class="text-muted mb-0">Track your order history and status</p>
                </div>
                <a href="<?= base_url('index.php') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                </a>
            </div>
            
            <!-- Order summary cards -->
            <div class="row g-3 mt-3">
                <div class="col-md-3 col-6">
                    <div class="card border-start border-5 border-primary shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Total Orders</h6>
                                    <h3 class="mb-0"><?= $totalOrders ?></h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-shopping-bag text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-6">
                    <div class="card border-start border-5 border-success shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Completed</h6>
                                    <h3 class="mb-0"><?= Order::count(['user_id' => $_SESSION['user_id'], 'status' => 'completed']) ?></h3>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-6">
                    <div class="card border-start border-5 border-warning shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Processing</h6>
                                    <h3 class="mb-0"><?= Order::count(['user_id' => $_SESSION['user_id'], 'status' => 'processing']) ?></h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-truck text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-6">
                    <div class="card border-start border-5 border-danger shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Cancelled</h6>
                                    <h3 class="mb-0"><?= Order::count(['user_id' => $_SESSION['user_id'], 'status' => 'cancelled']) ?></h3>
                                </div>
                                <div class="bg-danger bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-times-circle text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
                <h3 class="mb-3">No orders yet</h3>
                <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping to see your orders here.</p>
                <a href="<?= base_url('index.php') ?>" class="btn btn-primary px-4">
                    <i class="fas fa-shopping-cart me-2"></i> Browse Products
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <div class="text-muted small">
                        Showing <?= count($orders) ?> of <?= $totalOrders ?> orders
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Order #</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th class="pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): 
                                $statusClass = [
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'completed' => 'success',
                                    'cancelled' => 'danger'
                                ][$order['status']] ?? 'secondary';
                            ?>
                                <tr>
                                    <td class="ps-4 fw-bold">#<?= $order['order_number'] ?></td>
                                    <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                    <td>₵<?= number_format($order['total_amount'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $statusClass ?> rounded-pill">
                                            <i class="fas <?= 
                                                $order['status'] === 'completed' ? 'fa-check' : 
                                                ($order['status'] === 'processing' ? 'fa-truck' : 
                                                ($order['status'] === 'cancelled' ? 'fa-times' : 'fa-clock')) ?> 
                                                me-1"></i>
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </td>
                                    <td class="pe-4">
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary view-details-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#orderDetailsModal"
                                                    data-order-id="<?= $order['id'] ?>"
                                                    data-order-number="<?= $order['order_number'] ?>"
                                                    data-order-date="<?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?>"
                                                    data-order-total="₵<?= number_format($order['total_amount'], 2) ?>"
                                                    data-order-status="<?= ucfirst($order['status']) ?>"
                                                    data-order-payment="<?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?>">
                                                <i class="fas fa-eye me-1"></i> Details
                                            </button>
                                            <?php if ($order['status'] === 'pending'): ?>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#cancelModal"
                                                        data-order-id="<?= $order['id'] ?>">
                                                    <i class="fas fa-times me-1"></i> Cancel
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="card-footer bg-white border-top">
                        <nav aria-label="Orders pagination">
                            <ul class="pagination justify-content-center mb-0">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>">
                                        <i class="fas fa-chevron-left me-1"></i> Previous
                                    </a>
                                </li>
                                
                                <?php 
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                // Show first page if not in range
                                if ($startPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=1">1</a>
                                    </li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php // Show last page if not in range
                                if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $totalPages ?>"><?= $totalPages ?></a>
                                    </li>
                                <?php endif; ?>
                                
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>">
                                        Next <i class="fas fa-chevron-right ms-1"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Order #<span id="modalOrderNumber"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-calendar-alt text-muted me-3"></i>
                            <div>
                                <small class="text-muted">Order Date</small>
                                <p class="mb-0 fw-bold" id="modalOrderDate"></p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-credit-card text-muted me-3"></i>
                            <div>
                                <small class="text-muted">Payment Method</small>
                                <p class="mb-0 fw-bold" id="modalPaymentMethod"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-receipt text-muted me-3"></i>
                            <div>
                                <small class="text-muted">Total Amount</small>
                                <p class="mb-0 fw-bold" id="modalOrderTotal"></p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-tag text-muted me-3"></i>
                            <div>
                                <small class="text-muted">Status</small>
                                <p class="mb-0"><span class="badge rounded-pill" id="modalOrderStatus"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status Message Section -->
                <div class="alert mb-4" id="statusMessageContainer">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-2x me-3" id="statusIcon"></i>
                        <div>
                            <h6 class="alert-heading mb-1" id="statusTitle"></h6>
                            <p class="mb-1" id="statusMessage"></p>
                            <p class="mb-0" id="nextSteps"></p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-truck me-2"></i>Delivery Information</h6>
                            </div>
                            <div class="card-body">
                                <p id="deliveryAddress"></p>
                                <p id="customerPhone"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Order Summary</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Items:</strong> <span id="totalItems"></span></p>
                                <p><strong>Payment Status:</strong> <span id="paymentStatus"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h6 class="fw-bold mb-3"><i class="fas fa-shopping-basket me-2"></i>Order Items</h6>
                <div class="table-responsive">
                    <table class="table" id="orderItemsTable">
                        <thead class="bg-light">
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="viewFullDetailsBtn">
                    <i class="fas fa-file-alt me-1"></i> View Full Details
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/chantmo-supa/cancel-order.php">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Are you sure you want to cancel this order?
                    </div>
                    <input type="hidden" name="order_id" id="cancelOrderId">
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">Reason for cancellation</label>
                        <textarea class="form-control" id="cancelReason" name="reason" rows="3" required
                                  placeholder="Please let us know why you're canceling this order..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Go Back</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i> Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Custom styles for orders page */
    .card {
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card-header {
        font-weight: 600;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
    }
    
    .page-item.active .page-link {
        background-color: #6e8efb;
        border-color: #6e8efb;
    }
    
    .page-link {
        color: #6e8efb;
        min-width: 40px;
        text-align: center;
    }
    
    .view-details-btn:hover {
        background-color: #6e8efb;
        color: white;
    }
    
    /* Status badges */
    .bg-warning {
        background-color: #ffc107 !important;
        color: #212529;
    }
    
    .bg-info {
        background-color: #0dcaf0 !important;
        color: #000;
    }
    
    .bg-success {
        background-color: #198754 !important;
        color: white;
    }
    
    .bg-danger {
        background-color: #dc3545 !important;
        color: white;
    }
    
    /* Modal styling */
    .modal-content {
        border-radius: 12px;
    }
    
    .modal-header {
        border-bottom: none;
        padding: 1.5rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        border-top: none;
        padding: 1rem 1.5rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-header h5 {
            font-size: 1rem;
        }
        
        .table th, .table td {
            padding: 0.75rem 0.5rem;
        }
        
        .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Status messages configuration
    const statusMessages = {
        'pending': {
            'title': 'Order Received',
            'message': 'We\'ve received your order and will process it soon.',
            'icon': 'fa-clock',
            'next_steps': 'Please wait while we prepare your order.',
            'alert_class': 'alert-warning'
        },
        'processing': {
            'title': 'Processing Order',
            'message': 'Your order is being prepared for delivery.',
            'icon': 'fa-cog',
            'next_steps': 'We\'ll notify you when your order ships.',
            'alert_class': 'alert-info'
        },
        'completed': {
            'title': 'Order Completed',
            'message': 'Your order has been delivered successfully!',
            'icon': 'fa-check-circle',
            'next_steps': 'Thank you for shopping with us!',
            'alert_class': 'alert-success'
        },
        'cancelled': {
            'title': 'Order Cancelled',
            'message': 'This order has been cancelled.',
            'icon': 'fa-times-circle',
            'next_steps': 'Please contact us if you have any questions.',
            'alert_class': 'alert-danger'
        }
    };

    // Status badge classes
    const statusBadgeClasses = {
        'pending': 'bg-warning',
        'processing': 'bg-primary',
        'completed': 'bg-success',
        'cancelled': 'bg-danger'
    };

    // Set up order details modal
    const orderDetailsModal = document.getElementById('orderDetailsModal');
    if (orderDetailsModal) {
        orderDetailsModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const orderId = button.getAttribute('data-order-id');
            
            // Show loading state
            document.querySelector('#orderItemsTable tbody').innerHTML = 
                '<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 mb-0">Loading order items...</p></td></tr>';
            
            // Fetch order details via AJAX with error handling
            fetch(`/chantmo-supa/includes/get_order_details.php?order_id=${orderId}`, {
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { 
                        throw new Error(err.error || 'Failed to fetch order details') 
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Order data received:', data); // Debugging line
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                if (!data.order || !data.items) {
                    throw new Error('Invalid order data structure');
                }
                    
                const order = data.order;
                
                // Set basic order info
                document.getElementById('modalOrderNumber').textContent = order.order_number || 'N/A';
                document.getElementById('modalOrderDate').textContent = order.created_at ? 
                    new Date(order.created_at).toLocaleString() : 'Date not available';
                document.getElementById('modalOrderTotal').textContent = '₵' + 
                    (order.total_amount ? parseFloat(order.total_amount).toFixed(2) : '0.00');
                document.getElementById('modalPaymentMethod').textContent = order.payment_method ? 
                    order.payment_method.replace('_', ' ').toUpperCase() : 'Not specified';
                document.getElementById('deliveryAddress').textContent = order.address || 'Address not provided';
                document.getElementById('customerPhone').textContent = order.phone || 'Phone not provided';
                document.getElementById('paymentStatus').textContent = order.payment_status ? 
                    order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1) : 'Pending';
                    
                // Set status badge
                const statusBadge = document.getElementById('modalOrderStatus');
                statusBadge.textContent = order.status ? 
                    order.status.charAt(0).toUpperCase() + order.status.slice(1) : 'Pending';
                statusBadge.className = 'badge rounded-pill ' + 
                    (order.status ? statusBadgeClasses[order.status] : 'bg-secondary');
                
                // Set status message
                const statusInfo = order.status ? 
                    statusMessages[order.status] || statusMessages['pending'] : 
                    statusMessages['pending'];
                document.getElementById('statusTitle').textContent = statusInfo.title;
                document.getElementById('statusMessage').textContent = statusInfo.message;
                document.getElementById('nextSteps').innerHTML = statusInfo.next_steps ? 
                    `<strong>Next Steps:</strong> ${statusInfo.next_steps}` : '';
                document.getElementById('statusMessageContainer').className = `alert mb-4 ${statusInfo.alert_class}`;
                document.getElementById('statusIcon').className = `fas ${statusInfo.icon} fa-2x me-3`;
                
                // Set up "View Full Details" button
                document.getElementById('viewFullDetailsBtn').onclick = function() {
                    window.location.href = `/chantmo-supa/order-details.php?id=${order.id}`;
                };
                
                // Load order items
                const tbody = document.querySelector('#orderItemsTable tbody');
                tbody.innerHTML = '';
                
                if (data.items && Array.isArray(data.items) && data.items.length > 0) {
                    document.getElementById('totalItems').textContent = data.items.length;
                    
                    data.items.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>
                                <div class="d-flex align-items-center">
                                    ${item.image_url ? 
                                        `<img src="${item.image_url}" class="img-thumbnail me-3" style="width:60px;height:60px;object-fit:cover" alt="${item.name || 'Product'}">` : 
                                        '<div class="img-thumbnail me-3 d-flex align-items-center justify-content-center" style="width:60px;height:60px;background:#eee"><i class="fas fa-box text-muted"></i></div>'
                                    }
                                    <div>
                                        <h6 class="mb-1">${item.name || 'Unnamed Product'}</h6>
                                        <small class="text-muted">SKU: ${item.sku || 'N/A'}</small>
                                    </div>
                                </div>
                            </td>
                            <td>₵${item.price ? parseFloat(item.price).toFixed(2) : '0.00'}</td>
                            <td>${item.quantity || 1}</td>
                            <td class="fw-bold">₵${(parseFloat(item.price || 0) * parseInt(item.quantity || 1)).toFixed(2)}</td>
                        `;
                        tbody.appendChild(row);
                    });
                    
                    // Add subtotal row
                    const subtotalRow = document.createElement('tr');
                    subtotalRow.className = 'border-top';
                    subtotalRow.innerHTML = `
                        <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                        <td class="fw-bold">₵${order.total_amount ? parseFloat(order.total_amount).toFixed(2) : '0.00'}</td>
                    `;
                    tbody.appendChild(subtotalRow);
                } else {
                    console.warn('No items found for order:', orderId); // Debugging line
                    document.getElementById('totalItems').textContent = '0';
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">No items found in this order</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading order details:', error);
                document.querySelector('#orderItemsTable tbody').innerHTML = 
                    `<tr><td colspan="4" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Error: ${error.message || 'Failed to load order details'}
                    </td></tr>`;
            });
        });
    }
    
    // Set up cancel modal
    const cancelModal = document.getElementById('cancelModal');
    if (cancelModal) {
        cancelModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const orderId = button.getAttribute('data-order-id');
            document.getElementById('cancelOrderId').value = orderId;
        });
    }
});
</script>


<?php require_once __DIR__ . '/includes/footer.php'; ?>