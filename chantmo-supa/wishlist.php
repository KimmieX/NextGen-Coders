<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/Product.php';

// API Endpoints
if (isset($_GET['check_status'])) {
    header('Content-Type: application/json');
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'error' => 'Not logged in']);
        exit;
    }
    
    $productId = (int)($_GET['product_id'] ?? 0);
    if ($productId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'inWishlist' => Product::isInWishlist($_SESSION['user_id'], $productId),
        'wishlistCount' => Product::getWishlistCount($_SESSION['user_id'])
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'error' => 'Please login to manage wishlist']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $productId = (int)($_POST['product_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    try {
        if ($productId <= 0) {
            throw new Exception('Invalid product ID');
        }

        $result = [];
        
        switch ($action) {
    case 'add':
        if (Product::isInWishlist($userId, $productId)) {
            // Item already exists - treat as success
            $result = [
                'success' => true,
                'message' => 'Already in wishlist',
                'inWishlist' => true,
                'action' => 'add'
            ];
        } else {
            $added = Product::addToWishlist($userId, $productId);
            $result = [
                'success' => $added,
                'message' => $added ? 'Added to wishlist' : 'Failed to add',
                'inWishlist' => true,
                'action' => 'add'
            ];
        }
        break;
        
    case 'remove':
        if (!Product::isInWishlist($userId, $productId)) {
            // Item not in wishlist - treat as success
            $result = [
                'success' => true,
                'message' => 'Not in wishlist',
                'inWishlist' => false,
                'action' => 'remove'
            ];
        } else {
            $removed = Product::removeFromWishlist($userId, $productId);
            $result = [
                'success' => $removed,
                'message' => $removed ? 'Removed from wishlist' : 'Failed to remove',
                'inWishlist' => false,
                'action' => 'remove'
            ];
        }
        break;
        
    default:
        throw new Exception('Invalid action');
}
        
        // Get updated count
        $result['wishlistCount'] = Product::getWishlistCount($userId);
        $result['productId'] = $productId;
        
        echo json_encode($result);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// Regular Page View
require_once __DIR__ . '/includes/header.php';

$wishlistItems = isLoggedIn() ? Product::getWishlistItems($_SESSION['user_id']) : [];
$wishlistCount = count($wishlistItems);
?>

<main class="container mt-4">
    <div class="row">
        
        
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title mb-4">My Wishlist 
                        <span class="badge bg-primary wishlist-count"><?= $wishlistCount ?></span>
                    </h2>
                    
                    <?php if (empty($wishlistItems)): ?>
                        <div class="alert alert-info">
                            Your wishlist is empty. <a href="<?= base_url('index.php') ?>">Browse products</a>
                        </div>
                    <?php else: ?>
                        <div class="row g-4" id="wishlistContainer">
                            <?php foreach ($wishlistItems as $product): ?>
                                <div class="col-md-4 col-6 product-card" data-id="<?= $product->id ?>">
                                    <?php include __DIR__ . '/includes/product-card.php'; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize wishlist buttons
    document.querySelectorAll('.btn-wishlist').forEach(btn => {
        if (btn.classList.contains('active')) {
            btn.innerHTML = '<i class="fas fa-heart"></i> In Wishlist';
        }
    });

    // Handle wishlist updates
    document.addEventListener('wishlistUpdated', function(e) {
        const { productId, inWishlist, wishlistCount, action } = e.detail;
        
        // Update all wishlist counters
        document.querySelectorAll('.wishlist-count').forEach(el => {
            el.textContent = wishlistCount;
        });
        
        // Update all buttons for this product
        document.querySelectorAll(`.btn-wishlist[data-product-id="${productId}"]`).forEach(btn => {
            btn.classList.toggle('active', inWishlist);
            btn.innerHTML = inWishlist 
                ? '<i class="fas fa-heart"></i> In Wishlist' 
                : '<i class="fas fa-heart"></i> Add to Wishlist';
        });
        
        // If on wishlist page and item was removed
        if (action === 'remove' && window.location.pathname.includes('wishlist.php')) {
            const productCard = document.querySelector(`.product-card[data-id="${productId}"]`);
            if (productCard) {
                productCard.style.opacity = '0';
                setTimeout(() => {
                    productCard.remove();
                    
                    // Show empty message if no items left
                    if (!document.querySelector('#wishlistContainer .product-card')) {
                        document.querySelector('#wishlistContainer').innerHTML = `
                            <div class="alert alert-info">
                                Your wishlist is empty. <a href="<?= base_url('index.php') ?>">Browse products</a>
                            </div>
                        `;
                    }
                }, 300);
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>