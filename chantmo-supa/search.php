<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Product.php';

// Initialize the database connection
Product::setPDO($pdo);

// Initialize variables
$conditions = [];
$params = [];
$query = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'id';
$minSearchLength = 2; // Minimum characters required for search

// Prepare search conditions
if (!empty($query)) {
    if (strlen($query) < $minSearchLength) {
        $_SESSION['search_warning'] = "Please enter at least $minSearchLength characters for more specific results";
    } else {
        $conditions[] = "(name LIKE ? OR description LIKE ?)";
        $params[] = "%$query%";
        $params[] = "%$query%";
    }
}

if (!empty($category)) {
    $conditions[] = "category = ?";
    $params[] = $category;
}

// Determine sort order
$sortOrder = 'id DESC';
switch ($sort) {
    case 'price_asc':
        $sortOrder = 'price ASC';
        break;
    case 'price_desc':
        $sortOrder = 'price DESC';
        break;
    case 'newest':
        $sortOrder = 'id DESC';
        break;
    case 'relevance':
        if (!empty($query) && strlen($query) >= $minSearchLength) {
            $sortOrder = "(CASE WHEN name LIKE ? THEN 1 WHEN description LIKE ? THEN 2 ELSE 3 END)";
            array_push($params, "$query%", "%$query%");
        }
        break;
}

// Get search results
$products = Product::search($conditions, $params, $sortOrder);
$categories = Product::getCategories();

$pageTitle = 'Search Results';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Search Results Section -->
<section class="py-5">
    <div class="container">
        <?php if (isset($_SESSION['search_warning'])): ?>
            <div class="alert alert-info mb-4">
                <?= htmlspecialchars($_SESSION['search_warning']) ?>
                <?php unset($_SESSION['search_warning']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($query) || !empty($category)): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <?php if (!empty($query)): ?>
                        Search Results for "<?= htmlspecialchars($query) ?>"
                    <?php elseif (!empty($category)): ?>
                        Category: <?= htmlspecialchars($category) ?>
                    <?php endif; ?>
                </h2>
                <a href="<?= base_url('search.php') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Reset Search
                </a>
            </div>
        <?php else: ?>
            <div class="text-center mb-4">
                <h2>All Products</h2>
                <p class="text-muted">Browse our complete product catalog</p>
            </div>
        <?php endif; ?>
        
        <!-- Search Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="<?= base_url('search.php') ?>" method="get" class="row g-3" id="searchForm">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="q" 
                               placeholder="Search products (min <?= $minSearchLength ?> chars)" 
                               value="<?= htmlspecialchars($query) ?>"
                               minlength="<?= $minSearchLength ?>">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" 
                                    <?= $category === $cat ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="sort">
                            <option value="id" <?= $sort === 'id' ? 'selected' : '' ?>>Default Sorting</option>
                            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
                            <?php if (!empty($query) && strlen($query) >= $minSearchLength): ?>
                                <option value="relevance" <?= $sort === 'relevance' ? 'selected' : '' ?>>Relevance</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="<?= base_url('search.php') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if (count($products) > 0): ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-3">
                        <?php include __DIR__ . '/includes/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4>No products found</h4>
                    <p class="text-muted">
                        <?php if (!empty($query) || !empty($category)): ?>
                            No products match your search criteria. Try different keywords or categories.
                        <?php else: ?>
                            There are currently no products available.
                        <?php endif; ?>
                    </p>
                    <a href="<?= base_url('search.php') ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-store"></i> Browse All Products
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Enhanced Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Search Toggle Functionality
    const searchCollapse = document.getElementById('searchCollapse');
    
    if (searchCollapse) {
        // Auto-focus search input when expanded
        searchCollapse.addEventListener('shown.bs.collapse', function() {
            const searchInput = this.querySelector('input[name="q"]');
            if (searchInput) searchInput.focus();
        });
        
        // Close search when clicking outside
        document.addEventListener('click', function(e) {
            const searchToggle = document.querySelector('[data-bs-target="#searchCollapse"]');
            if (!searchCollapse.contains(e.target) && !searchToggle.contains(e.target)) {
                const bsCollapse = bootstrap.Collapse.getInstance(searchCollapse);
                if (bsCollapse) bsCollapse.hide();
            }
        });
    }
    
    // Enhanced search form validation
    const searchForm = document.querySelector('#searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="q"]');
            const searchValue = searchInput.value.trim();
            
            // Prevent empty searches
            if (searchValue === '') {
                e.preventDefault();
                searchInput.focus();
                return;
            }
            
            // Enforce minimum length
            if (searchValue.length > 0 && searchValue.length < <?= $minSearchLength ?>) {
                e.preventDefault();
                alert('Please enter at least <?= $minSearchLength ?> characters to search');
                searchInput.focus();
            }
        });
    }
    
    // Live search character counter
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        const charCounter = document.createElement('small');
        charCounter.className = 'form-text text-muted mt-1';
        charCounter.style.display = 'none';
        searchInput.parentNode.appendChild(charCounter);
        
        searchInput.addEventListener('input', function() {
            const remaining = <?= $minSearchLength ?> - this.value.length;
            if (this.value.length > 0 && remaining > 0) {
                charCounter.textContent = `${remaining} more character${remaining > 1 ? 's' : ''} needed`;
                charCounter.style.display = 'block';
                charCounter.className = 'form-text text-warning mt-1';
            } else if (this.value.length >= <?= $minSearchLength ?>) {
                charCounter.textContent = 'Ready to search!';
                charCounter.style.display = 'block';
                charCounter.className = 'form-text text-success mt-1';
            } else {
                charCounter.style.display = 'none';
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>