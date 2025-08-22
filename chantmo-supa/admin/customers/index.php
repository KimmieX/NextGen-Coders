<?php
$pageTitle = 'Manage Customers';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

// Check if admin is logged in
if (!isLoggedIn() || !isAdmin()) {
    redirect('/chantmo-supa/admin/login.php');
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_customer'])) {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid CSRF token";
        redirect('/chantmo-supa/admin/customers/');
    }

    $customerId = (int)$_POST['customer_id'];
    
    try {
        // Check if customer exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$customerId]);
        
        if (!$stmt->fetch()) {
            throw new Exception("Customer not found");
        }
        
        // Delete customer
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$customerId]);
        
        $_SESSION['success'] = "Customer deleted successfully!";
        redirect('/chantmo-supa/admin/customers/');
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        redirect('/chantmo-supa/admin/customers/');
    }
}

// Fetch all customers
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$customers = $stmt->fetchAll();

// Generate CSRF token
generateCsrfToken();

// Use the new admin header
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Customer Management</h1>
    </div>

    <?php displayMessage(); ?>
    
    <!-- Customers Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?= $customer['id'] ?></td>
                            <td><?= htmlspecialchars($customer['username']) ?></td>
                            <td><?= htmlspecialchars($customer['email']) ?></td>
                            <td><?= htmlspecialchars($customer['phone']) ?></td>
                            <td><?= htmlspecialchars($customer['address']) ?></td>
                            <td><?= date('M j, Y', strtotime($customer['created_at'])) ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
                                    <button type="submit" name="delete_customer" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Are you sure you want to delete this customer? All their orders will also be deleted.')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
// Use the new admin footer
require_once __DIR__ . '/../includes/admin_footer.php'; 
?>