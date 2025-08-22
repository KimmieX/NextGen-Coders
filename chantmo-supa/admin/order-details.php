<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/Order.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/chantmo-supa/admin/login.php');
}

$orderId = $_GET['id'] ?? 0;
$order = Order::getOrderWithItems($orderId);

if (!$order) {
    $_SESSION['error'] = "Order not found";
    redirect('/chantmo-supa/admin/orders.php');
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Order #<?= htmlspecialchars($order['order_number']) ?></h1>
        <a href="orders.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($item['image_url']): ?>
                                            <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                                 class="img-thumbnail me-3" 
                                                 style="width: 60px; height: 60px; object-fit: cover;" 
                                                 alt="<?= htmlspecialchars($item['name']) ?>">
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                                <small class="text-muted">SKU: <?= $item['product_id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>₵<?= number_format($item['price'], 2) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td>₵<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total</td>
                                    <td class="fw-bold">₵<?= number_format($order['total_amount'], 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Admin Notes Section -->
            <!-- Replace the admin notes section with this enhanced version -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Admin Notes</h5>
        <small class="text-muted">Last updated: <?= !empty($order['admin_notes']) ? date('M j, Y h:i A', strtotime($order['updated_at'])) : 'Never' ?></small>
    </div>
    <div class="card-body">
        <form id="adminNotesForm">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="hidden" name="action" value="save_admin_notes">
    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
    
    <div class="mb-3">
    <textarea class="form-control" name="admin_notes" rows="5" 
              placeholder="Add notes using simple formatting:
              
* Start with date: [8/12] 
* Use - for actions
* Wrap urgent items in **double asterisks**
* `code` for order numbers"><?= 
        htmlspecialchars($order['admin_notes'] ?? '') 
    ?></textarea>
</div>
    <button type="submit" class="btn btn-primary">
        <span class="submit-text">Save Notes</span>
        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
    </button>
</form>
        
        <!-- Notes History -->
        <?php $notesHistory = Order::getOrderNotes($order['id']); ?>
        <?php if (!empty($notesHistory)): ?>
        <div class="mt-4">
            <h6>Notes History</h6>
            <div class="list-group">
                <?php foreach ($notesHistory as $note): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <strong><?= htmlspecialchars($note['username']) ?></strong>
                        <small class="text-muted"><?= date('M j, Y h:i A', strtotime($note['created_at'])) ?></small>
                    </div>
                    <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($note['note'])) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
    <div class="card-header">
        <h5>Customer Information</h5>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <h6 class="small text-muted mb-1">Name</h6>
            <p class="mb-0"><?= htmlspecialchars($order['username'] ?? 'N/A') ?></p>
        </div>
        
        <div class="mb-3">
            <h6 class="small text-muted mb-1">Email</h6>
            <p class="mb-0"><?= htmlspecialchars($order['email'] ?? 'N/A') ?></p>
        </div>
        
        <div class="mb-3">
            <h6 class="small text-muted mb-1">Phone</h6>
            <p class="mb-0"><?= htmlspecialchars($order['phone'] ?? 'Not provided') ?></p>
        </div>
        
        <div class="mb-3">
            <h6 class="small text-muted mb-1">Address</h6>
            <p class="mb-0"><?= nl2br(htmlspecialchars($order['address'] ?? 'Not provided')) ?></p>
        </div>
    </div>
</div>

            <div class="card">
                <div class="card-header">
                    <h5>Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="small text-muted mb-1">Order Status</h6>
                        <p class="mb-0">
                            <span class="badge bg-<?= [
                                'pending' => 'warning',
                                'processing' => 'primary',
                                'completed' => 'success',
                                'cancelled' => 'danger'
                            ][$order['status']] ?? 'secondary' ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="small text-muted mb-1">Payment Method</h6>
                        <p class="mb-0"><?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="small text-muted mb-1">Payment Status</h6>
                        <p class="mb-0">
                            <span class="badge bg-<?= $order['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                <?= ucfirst($order['payment_status']) ?>
                            </span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="small text-muted mb-1">Order Date</h6>
                        <p class="mb-0"><?= date('M j, Y h:i A', strtotime($order['created_at'])) ?></p>
                    </div>
                    
                    <!-- Status Update Form -->
                    <form method="POST" action="<?= base_url('admin/orders.php') ?>" class="mt-4">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Update Status</label>
                            <select class="form-select" name="status" required>
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>

                    <!-- Add this near the status update form -->
<form method="POST" action="<?= base_url('admin/orders.php') ?>" class="mt-3">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="hidden" name="action" value="update_payment_status">
    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
    
    <div class="mb-3">
        <label class="form-label">Update Payment Status</label>
        <select class="form-select" name="payment_status" required>
            <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary w-100">Update Payment Status</button>
</form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('adminNotesForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const submitText = form.querySelector('.submit-text');
    const spinner = form.querySelector('.spinner-border');
    
    // Show loading state
    submitText.textContent = 'Saving...';
    spinner.classList.remove('d-none');
    submitBtn.disabled = true;
    
    try {
        const formData = new FormData(form);
        const response = await fetch('<?= base_url("admin/update_order.php") ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Failed to save notes');
        }
        
        // Show success toast
        showToast('Success', 'Admin notes saved successfully!', 'success');
        
        // Update last updated time if element exists
        const lastUpdatedEl = form.closest('.card').querySelector('.text-muted');
        if (lastUpdatedEl) {
            const now = new Date();
            lastUpdatedEl.textContent = 'Last updated: ' + now.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }
        
    } catch (error) {
        console.error('Error:', error);
        // Show error toast
        showToast('Error', error.message || 'Failed to save notes', 'danger');
    } finally {
        // Reset button state
        submitText.textContent = 'Save Notes';
        spinner.classList.add('d-none');
        submitBtn.disabled = false;
    }
});

// Add this to order-details.php's script section
const notesTextarea = document.querySelector('textarea[name="admin_notes"]');
const notesPreview = document.createElement('div');
notesPreview.className = 'notes-preview bg-light p-2 mt-2 rounded';
notesTextarea?.parentNode?.appendChild(notesPreview);

notesTextarea?.addEventListener('input', function() {
    const timestamp = new Date().toLocaleString('en-US', {
        month: 'numeric',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    notesPreview.innerHTML = `
        <small class="text-muted">Preview:</small>
        <div class="mt-1 p-2 bg-white rounded">
            ${formatNotes(`[${timestamp}] ${$_SESSION['username']}:\n` + this.value)}
        </div>
    `;
});

function formatNotes(text) {
    // Simple markdown formatting
    return text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\n/g, '<br>')
        .replace(/`(.*?)`/g, '<code>$1</code>');
}
</script>

<?php
require_once __DIR__ . '/includes/admin_footer.php';
?>