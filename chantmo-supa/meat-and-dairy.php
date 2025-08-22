<?php
$pageTitle = 'Meat & Dairy';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Product.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

// Pagination setup
$perPage = 8;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Sorting setup
$sort = $_GET['sort'] ?? 'newest';
$sortOptions = [
    'newest' => ['field' => 'created_at', 'order' => 'DESC'],
    'price_asc' => ['field' => 'price', 'order' => 'ASC'],
    'price_desc' => ['field' => 'price', 'order' => 'DESC'],
    'popular' => ['field' => 'view_count', 'order' => 'DESC'],
    'discount' => ['field' => '(original_price - price)', 'order' => 'DESC'],
    'expiry' => ['field' => 'expiry_date', 'order' => 'ASC'] // Added expiry sorting for perishables
];

// Validate sort option
if (!array_key_exists($sort, $sortOptions)) {
    $sort = 'newest';
}

$sortField = $sortOptions[$sort]['field'];
$sortOrder = $sortOptions[$sort]['order'];

// Get meat and dairy products with error handling
try {
    $meatDairyProducts = Product::getByCategory('Dairy & Meat', $perPage, $offset, $sortField, $sortOrder);
    $totalProducts = Product::count(['category' => 'Dairy & Meat']);
    $totalPages = ceil($totalProducts / $perPage);
} catch (Exception $e) {
    error_log("Error loading meat and dairy products: " . $e->getMessage());
    $meatDairyProducts = [];
    $totalProducts = 0;
    $totalPages = 1;
    echo '<div class="alert alert-danger">Error loading meat and dairy products. Please try again later.</div>';
}

// Get recently viewed products
$recentlyViewed = [];
if (!empty($_SESSION['recently_viewed'])) {
    try {
        $recentlyViewedIds = array_slice($_SESSION['recently_viewed'], 0, 4);
        $recentlyViewedIds = array_filter($recentlyViewedIds, 'is_numeric');
        
        if (!empty($recentlyViewedIds)) {
            $placeholders = implode(',', array_fill(0, count($recentlyViewedIds), '?'));
            $sql = "SELECT * FROM products WHERE id IN ($placeholders) ORDER BY FIELD(id, " . 
                   implode(',', $recentlyViewedIds) . ")";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($recentlyViewedIds);
            $recentlyViewed = $stmt->fetchAll(PDO::FETCH_CLASS, 'Product');
        }
    } catch (PDOException $e) {
        error_log("Recently viewed query failed: " . $e->getMessage());
    }
}
?>

<!-- Hero Section -->
<section class="hero-section py-5 bg-gradient-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Fresh Meat & Dairy</h1>
                <p class="lead mb-4">Premium quality meats and dairy products, sourced fresh for your family.</p>
                <div class="d-flex gap-3">
                    <a href="<?= base_url('new-products.php') ?>" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-star me-2"></i> New Arrivals
                    </a>
                    <a href="<?= base_url('deals.php') ?>" class="btn btn-outline-primary btn-lg px-4">
                        <i class="fas fa-tag me-2"></i> Special Offers
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="https://media.istockphoto.com/id/861188910/photo/different-types-of-food-on-rustic-wooden-table.webp?a=1&b=1&s=612x612&w=0&k=20&c=cmMfJEPSnW8AfWcF-vyICsoxRZU1HrYfWu_jqT1HRVk=" 
                     alt="Fresh Meat and Dairy" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>
</section>


<!-- Meat & Dairy Products Grid -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-2">Our Meat & Dairy Selection</h2>
                <p class="text-muted">
                    <?= $totalProducts ?> products available
                    <?php if ($totalPages > 1): ?>
                        (Page <?= $page ?> of <?= $totalPages ?>)
                    <?php endif; ?>
                </p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-sort me-2"></i>
                    Sort: <?= ucfirst(str_replace('_', ' ', $sort)) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Sort Options</h6></li>
                    <li><a class="dropdown-item <?= $sort === 'newest' ? 'active' : '' ?>" 
                           href="?sort=newest&page=1"><i class="fas fa-clock me-2"></i> Newest First</a></li>
                    <li><a class="dropdown-item <?= $sort === 'price_asc' ? 'active' : '' ?>" 
                           href="?sort=price_asc&page=1"><i class="fas fa-arrow-up me-2"></i> Price: Low to High</a></li>
                    <li><a class="dropdown-item <?= $sort === 'price_desc' ? 'active' : '' ?>" 
                           href="?sort=price_desc&page=1"><i class="fas fa-arrow-down me-2"></i> Price: High to Low</a></li>
                    <li><a class="dropdown-item <?= $sort === 'popular' ? 'active' : '' ?>" 
                           href="?sort=popular&page=1"><i class="fas fa-fire me-2"></i> Most Popular</a></li>
                    <li><a class="dropdown-item <?= $sort === 'discount' ? 'active' : '' ?>" 
                           href="?sort=discount&page=1"><i class="fas fa-percentage me-2"></i> Highest Discount</a></li>
                    <li><a class="dropdown-item <?= $sort === 'expiry' ? 'active' : '' ?>" 
                           href="?sort=expiry&page=1"><i class="fas fa-hourglass-half me-2"></i> Expiry Soonest</a></li>
                </ul>
            </div>
        </div>

        <?php if (empty($meatDairyProducts)): ?>
            <div class="text-center py-5">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No meat or dairy products available at the moment. Check back soon!
                </div>
                <a href="<?= base_url() ?>" class="btn btn-primary mt-3">
                    <i class="fas fa-home me-2"></i> Return Home
                </a>
            </div>
        <?php else: ?>
            <!-- Group products in rows of 4 with spacing -->
            <?php foreach (array_chunk($meatDairyProducts, 4) as $productGroup): ?>
                <div class="row g-4 mb-4">
                    <?php foreach ($productGroup as $product): ?>
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <?php 
                            // Highlight products expiring soon
                            $expiryClass = '';
                            if ($product->expiry_date) {
                                $expiryDate = new DateTime($product->expiry_date);
                                $today = new DateTime();
                                $diff = $today->diff($expiryDate);
                                if ($diff->days <= 3) {
                                    $expiryClass = 'border-danger border-2';
                                }
                            }
                            ?>
                            <div class="product-card <?= $expiryClass ?>" data-id="<?= $product->id ?>">
                                <?php include __DIR__ . '/includes/product-card.php'; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="w-100 mb-4"></div> <!-- Spacer between groups -->
            <?php endforeach; ?>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?sort=<?= $sort ?>&page=<?= $page - 1 ?>">
                                <i class="fas fa-chevron-left me-1"></i> Previous
                            </a>
                        </li>
                        
                        <?php 
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?sort=<?= $sort ?>&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?sort=<?= $sort ?>&page=<?= $page + 1 ?>">
                                Next <i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Storage Tips Section -->
<section class="storage-tips py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold text-center mb-5">Meat & Dairy Storage Tips</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-primary text-white mb-3 mx-auto">
                            <i class="fas fa-snowflake fa-2x"></i>
                        </div>
                        <h4 class="mb-3">Refrigeration</h4>
                        <p class="text-muted">Keep dairy products at 4°C or below and meats at 2°C for optimal freshness.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-primary text-white mb-3 mx-auto">
                            <i class="fas fa-box-open fa-2x"></i>
                        </div>
                        <h4 class="mb-3">Proper Packaging</h4>
                        <p class="text-muted">Store meats in airtight containers or freezer bags to prevent freezer burn.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-primary text-white mb-3 mx-auto">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h4 class="mb-3">Consumption Timeline</h4>
                        <p class="text-muted">Use fresh meats within 3-5 days and dairy within 7 days of opening.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section bg-primary text-white py-5">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-3">Freshness Alerts & Offers</h2>
                <p class="lead mb-4">Subscribe to get updates on our freshest arrivals and special deals on meat and dairy.</p>
                
                <form class="newsletter-form" id="meatDairyNewsletterForm" action="<?= base_url('includes/newsletter-subscribe.php') ?>" method="POST">
                    <div class="input-group input-group-lg mx-auto" style="max-width: 500px;">
                        <input type="email" name="email" 
                               class="form-control" 
                               placeholder="Your email address" 
                               required
                               aria-label="Email for newsletter subscription">
                        <button class="btn btn-light" type="submit">
                            <i class="fas fa-paper-plane me-2"></i> Subscribe
                        </button>
                    </div>
                    <div class="form-text mt-2 text-white-50">
                        We'll notify you about fresh arrivals. Unsubscribe anytime.
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Recently Viewed Section -->
<?php if (!empty($recentlyViewed)): ?>
<section class="recently-viewed py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">
                <i class="fas fa-clock me-2"></i> Recently Viewed
            </h2>
            <a href="<?= base_url('products.php') ?>" class="btn btn-outline-primary">
                View All Products
            </a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($recentlyViewed as $product): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <?php include __DIR__ . '/includes/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
    .hero-section {
        background: linear-gradient(135deg, rgba(110, 142, 251, 0.1) 0%, rgba(167, 119, 227, 0.1) 100%);
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .newsletter-section {
        background: linear-gradient(135deg, #6e8efb, #a777e3);
    }
    
    .recently-viewed {
        border-top: 1px solid rgba(0,0,0,0.1);
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    
    .page-item.active .page-link {
        background-color: #6e8efb;
        border-color: #6e8efb;
    }
    
    .page-link {
        color: #6e8efb;
        min-width: 50px;
        text-align: center;
    }
    
    .icon-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .storage-tips .card {
        transition: transform 0.3s ease;
    }
    
    .storage-tips .card:hover {
        transform: translateY(-5px);
    }
</style>

<script>
// Newsletter Form Handling for meat and dairy page
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.getElementById('meatDairyNewsletterForm');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing...';
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Reset button state
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                
                // Show appropriate toast notification
                if (data.status && data.message) {
                    showNewsletterToast(data.status, data.message);
                } else {
                    showNewsletterToast('error', 'Unexpected response from server');
                }
                
                // Reset form if success
                if (data.status === 'success') {
                    form.reset();
                }
            })
            .catch(error => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                showNewsletterToast('error', 'Failed to submit. Please check your connection and try again.');
                console.error('Newsletter submission error:', error);
            });
        });
    }
    
    // Check for verification success in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('newsletter_verified')) {
        showNewsletterToast('success', 'Subscription confirmed! Check your email for your discount code.');
        // Clean URL without reload
        history.replaceState(null, '', window.location.pathname);
    }
});

// Toast Notification Function
function showNewsletterToast(type, message) {
    // Remove any existing toasts first
    document.querySelectorAll('.newsletter-toast').forEach(toast => toast.remove());
    
    const toastContainer = document.createElement('div');
    toastContainer.className = 'position-fixed bottom-0 end-0 p-3 newsletter-toast';
    toastContainer.style.zIndex = '1090';
    
    // Determine icon and colors
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        info: 'fa-info-circle'
    };
    const icon = icons[type] || 'fa-bell';
    const bgClass = `bg-${type}`;
    
    toastContainer.innerHTML = `
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header ${bgClass} text-white">
                <strong class="me-auto">
                    <i class="fas ${icon} me-2"></i>
                    ${type.charAt(0).toUpperCase() + type.slice(1)}
                </strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-white text-dark">
                ${message}
            </div>
        </div>
    `;
    
    document.body.appendChild(toastContainer);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        toastContainer.remove();
    }, 5000);
    
    // Add click handler for close button
    const closeBtn = toastContainer.querySelector('.btn-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            toastContainer.remove();
        });
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>