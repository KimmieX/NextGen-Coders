<?php
$pageTitle = 'Order Management';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/Order.php';

// Check admin authentication
if (!isLoggedIn() || !isAdmin()) {
    redirect(base_url('admin/login.php'));
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid CSRF token";
        redirect(base_url('admin/orders.php'));
    }

    $action = $_POST['action'] ?? '';
    $orderId = (int)($_POST['order_id'] ?? 0);

    try {
        switch ($action) {
            case 'update_status':
                $newStatus = $_POST['status'] ?? '';
                if (Order::updateStatus($orderId, $newStatus)) {
                    $_SESSION['success'] = "Order status updated successfully!";
                } else {
                    $_SESSION['error'] = "Failed to update order status";
                }
                break;

            case 'update_payment_status':
                $newStatus = $_POST['payment_status'] ?? '';
                if (Order::updatePaymentStatus($orderId, $newStatus)) {
                    $_SESSION['success'] = "Payment status updated successfully!";
                } else {
                    $_SESSION['error'] = "Failed to update payment status";
                }
                break;

            case 'delete':
                if (Order::delete($orderId)) {
                    $_SESSION['success'] = "Order deleted successfully!";
                } else {
                    $_SESSION['error'] = "Failed to delete order";
                }
                break;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    redirect(base_url('admin/orders.php'));
}

// Clean and prepare filters
$filters = [
    'status' => trim($_GET['status'] ?? ''),
    'payment_method' => trim($_GET['payment_method'] ?? ''),
    'date_from' => trim($_GET['date_from'] ?? ''),
    'date_to' => trim($_GET['date_to'] ?? ''),
    'order_number' => trim($_GET['order_number'] ?? ''),
    'customer' => trim($_GET['customer'] ?? '')
];

// Remove empty filters
$filters = array_filter($filters, function($value) {
    return $value !== '';
});

// Get filtered orders
$orders = Order::getAll($filters);

// Use the new admin header
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="container-fluid">
    
    <?php displayMessage(); ?>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Orders</h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <div class="btn-group ms-2">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= base_url('admin/export_orders.php?export=csv&') ?><?= http_build_query($_GET) ?>">
                                <i class="bi bi-file-earmark-spreadsheet"></i> CSV Format
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): 
                            $statusClass = [
                                'pending' => 'bg-warning',
                                'processing' => 'bg-primary',
                                'completed' => 'bg-success',
                                'cancelled' => 'bg-danger'
                            ][$order['status']] ?? 'bg-secondary';
                        ?>
                            <tr>
                                <td>#<?= htmlspecialchars($order['order_number']) ?></td>
                                <td><?= htmlspecialchars($order['username']) ?></td>
                                <td><?= date('M j, Y h:i A', strtotime($order['created_at'])) ?></td>
                                <td>₵<?= number_format($order['total_amount'], 2) ?></td>
                                <td><?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></td>
                                <td>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- View Button -->
                                        <a href="<?= base_url('admin/order-details.php') ?>?id=<?= $order['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        <!-- Edit Button -->
                                        <button class="btn btn-sm btn-primary edit-order" 
                                            data-order-id="<?= $order['id'] ?>"
                                            data-current-status="<?= $order['status'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <!-- Delete Button -->
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this order?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Order Modal -->
<div class="modal fade" id="editOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= base_url('admin/orders.php') ?>">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="order_id" id="modalOrderId">
                    
                    <div class="mb-3">
                        <label for="orderStatus" class="form-label">Status</label>
                        <select class="form-select" id="orderStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="<?= base_url('admin/orders.php') ?>">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="processing" <?= ($_GET['status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="payment_method">
                                <option value="">All Methods</option>
                                <option value="cash" <?= ($_GET['payment_method'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash</option>
                                <option value="mobile_money" <?= ($_GET['payment_method'] ?? '') === 'mobile_money' ? 'selected' : '' ?>>Mobile Money</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date From</label>
                            <input type="date" class="form-control" name="date_from" value="<?= $_GET['date_from'] ?? '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date To</label>
                            <input type="date" class="form-control" name="date_to" value="<?= $_GET['date_to'] ?? '' ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Order Number</label>
                            <input type="text" class="form-control" name="order_number" value="<?= $_GET['order_number'] ?? '' ?>" placeholder="ORD-12345">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Customer</label>
                            <input type="text" class="form-control" name="customer" value="<?= $_GET['customer'] ?? '' ?>" placeholder="Customer name or email">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="<?= base_url('admin/orders.php') ?>" class="btn btn-secondary">Reset</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit button clicks
    document.querySelectorAll('.edit-order').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const currentStatus = this.getAttribute('data-current-status');
            
            // Set values in modal
            document.getElementById('modalOrderId').value = orderId;
            document.getElementById('orderStatus').value = currentStatus;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editOrderModal'));
            modal.show();
        });
    });
});
</script>

<?php
// Use the new admin footer
require_once __DIR__ . '/includes/admin_footer.php';
?>