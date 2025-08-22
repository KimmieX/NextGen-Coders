<?php
$pageTitle = 'Manage Products';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/Product.php';

// Check if admin is logged in
if (!isLoggedIn() || !isAdmin()) {
    redirect('/chantmo-supa/admin/login.php');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid CSRF token";
        redirect('/chantmo-supa/admin/products/');
    }

    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'delete') {
            if (empty($_POST['id'])) {
                throw new Exception("Product ID is required for deletion");
            }
            
            if (!Product::delete((int)$_POST['id'])) {
                throw new Exception("Failed to delete product");
            }
            
            $_SESSION['success'] = "Product deleted successfully!";
            redirect('/chantmo-supa/admin/products/');
        }
        
        // Validate for create/update actions
        $requiredFields = ['name', 'price', 'category'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        // Prepare product data
        $productData = [
            'name' => htmlspecialchars(trim($_POST['name'])),
            'price' => (float)$_POST['price'],
            'original_price' => !empty($_POST['original_price']) ? (float)$_POST['original_price'] : (float)$_POST['price'],
            'stock_quantity' => (int)$_POST['stock_quantity'],
            'category' => $_POST['category'],
            'image_url' => filter_var($_POST['image_url'], FILTER_SANITIZE_URL),
            'description' => htmlspecialchars(trim($_POST['description'] ?? '')),
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'badge' => $_POST['badge'] ?: null,
            'expiry_date' => !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null
        ];

        if ($action === 'create') {
            $product = new Product($productData);
            $product->save();
            $_SESSION['success'] = "Product created successfully!";
        } 
        elseif ($action === 'update') {
            if (empty($_POST['id'])) {
                throw new Exception("Product ID is required for update");
            }
            
            $product = Product::getById((int)$_POST['id']);
            if (!$product) {
                throw new Exception("Product not found");
            }
            
            foreach ($productData as $key => $value) {
                $product->$key = $value;
            }
            
            $product->save();
            $_SESSION['success'] = "Product updated successfully!";
        }
        
        redirect('/chantmo-supa/admin/products/');
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        $_SESSION['form_data'] = $_POST;
        redirect('/chantmo-supa/admin/products/' . ($action === 'update' && !empty($_POST['id']) ? "?action=edit&id=".$_POST['id'] : ''));
    }
}

// Fetch all products
$products = Product::getAll();

// Use the new admin header
require_once __DIR__ . '/../includes/admin_header.php';
?>


    <?php displayMessage(); ?>
    
    <!-- Add Product Button -->
    <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#productModal" data-action="create">
        <i class="bi bi-plus-circle"></i> Add Product
    </button>

    <!-- Products Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price (₵)</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product->id ?></td>
                            <td>
                                <?php if ($product->image_url): ?>
                                <img src="<?= htmlspecialchars($product->image_url) ?>" class="admin-product-thumb">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($product->name) ?>
                                <?php if ($product->badge): ?>
                                    <span class="badge bg-info ms-2"><?= htmlspecialchars($product->badge) ?></span>
                                <?php endif; ?>
                                <?php if ($product->featured): ?>
                                    <span class="badge bg-warning ms-2">Featured</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                ₵<?= number_format($product->price, 2) ?>
                                <?php if ($product->hasDiscount()): ?>
                                    <br><small class="text-muted text-decoration-line-through">₵<?= number_format($product->original_price, 2) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= $product->stock_quantity ?>
                                <?php if ($product->isOutOfStock()): ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($product->category) ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-outline-primary edit-product" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#productModal"
                                        data-action="update"
                                        data-id="<?= $product->id ?>"
                                        data-name="<?= htmlspecialchars($product->name) ?>"
                                        data-description="<?= htmlspecialchars($product->description) ?>"
                                        data-price="<?= $product->price ?>"
                                        data-original-price="<?= $product->original_price ?>"
                                        data-stock-quantity="<?= $product->stock_quantity ?>"
                                        data-category="<?= htmlspecialchars($product->category) ?>"
                                        data-image-url="<?= htmlspecialchars($product->image_url) ?>"
                                        data-featured="<?= $product->featured ?>"
                                        data-badge="<?= htmlspecialchars($product->badge) ?>"
                                        data-expiry-date="<?= $product->expiry_date ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    
                                    <!-- Delete Button -->
                                    <form method="POST" class="delete-form">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $product->id ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
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

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="productForm">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="productId">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Beverages">Beverages</option>
                                <option value="Snacks & Sweets">Snacks & Sweets</option>
                                <option value="Cleaning Supplies">Cleaning Supplies</option>
                                <option value="Canned Goods">Canned Goods</option>
                                <option value="Dairy & Meat">Dairy & Meat</option>
                                <option value="Personal Care">Personal Care</option>
                            </select>
                        </div>
                        
                        <!-- Pricing -->
                        <div class="col-md-4">
                            <label for="price" class="form-label">Current Price (₵) *</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="original_price" class="form-label">Original Price (₵)</label>
                            <input type="number" class="form-control" id="original_price" name="original_price" step="0.01" min="0">
                            <small class="text-muted">Leave empty to use current price</small>
                        </div>
                        <div class="col-md-4">
                            <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" required>
                        </div>
                        
                        <!-- Image -->
                        <div class="col-md-6">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" class="form-control" id="image_url" name="image_url">
                            <small class="text-muted">Paste a direct image URL</small>
                        </div>
                        
                        <!-- Badge & Featured -->
                        <div class="col-md-6">
                            <label for="badge" class="form-label">Badge</label>
                            <select class="form-select" id="badge" name="badge">
                                <option value="">None</option>
                                <option value="New">New</option>
                                <option value="Sale">Sale</option>
                                <option value="Best Seller">Best Seller</option>
                                <option value="Limited">Limited</option>
                            </select>
                        </div>
                        
                        <!-- Expiry Date -->
                        <div class="col-md-6">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-4 pt-2">
                                <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1">
                                <label class="form-check-label" for="featured">Featured Product</label>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Handle edit button clicks
document.querySelectorAll('.edit-product').forEach(btn => {
    btn.addEventListener('click', function() {
        const action = this.getAttribute('data-action');
        document.getElementById('formAction').value = action;
        document.getElementById('modalTitle').textContent = action === 'update' ? 'Edit Product' : 'Add Product';
        
        // Set form values from data attributes
        document.getElementById('productId').value = this.getAttribute('data-id');
        document.getElementById('name').value = this.getAttribute('data-name');
        document.getElementById('description').value = this.getAttribute('data-description');
        document.getElementById('price').value = this.getAttribute('data-price');
        document.getElementById('original_price').value = this.getAttribute('data-original-price');
        document.getElementById('stock_quantity').value = this.getAttribute('data-stock-quantity');
        document.getElementById('category').value = this.getAttribute('data-category');
        document.getElementById('image_url').value = this.getAttribute('data-image-url');
        document.getElementById('featured').checked = this.getAttribute('data-featured') == 1;
        document.getElementById('badge').value = this.getAttribute('data-badge');
        document.getElementById('expiry_date').value = this.getAttribute('data-expiry-date');
    });
});

// Reset form when modal is shown for adding
document.getElementById('productModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    if (button.getAttribute('data-action') === 'create') {
        document.getElementById('productForm').reset();
        document.getElementById('formAction').value = 'create';
        document.getElementById('modalTitle').textContent = 'Add New Product';
    }
});

// Delete confirmation
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>