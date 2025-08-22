<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/Cart.php';
require_once __DIR__ . '/includes/Product.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Handle clear cart request
    if (isset($_POST['clear_cart'])) {
        Cart::clear();
        echo json_encode([
            'success' => true,
            'message' => 'Your cart has been cleared successfully',
            'totalItems' => 0,
            'totalPrice' => 0,
            'cartEmpty' => true
        ]);
        exit;
    }

    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    
    if ($productId <= 0) {
        throw new Exception('Invalid product ID. Please try again.');
    }

    $product = Product::getById($productId);
    if (!$product) {
        throw new Exception('Product not found. It may have been removed.');
    }

    if ($quantity > 0) {
        // Check stock before updating
        if ($quantity > $product->stock_quantity) {
            throw new Exception("Sorry, only {$product->stock_quantity} available in stock");
        }
        
        // Validate the product is still in stock
        if ($product->stock_quantity <= 0) {
            throw new Exception('This product is currently out of stock');
        }
        
        Cart::updateQuantity($productId, $quantity);
    } else {
        Cart::removeItem($productId);
    }
    
    $cartDetails = Cart::getCartDetails();
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated successfully',
        'totalItems' => $cartDetails['count'],
        'totalPrice' => $cartDetails['total'],
        'cartEmpty' => empty($cartDetails['items'])
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

exit;
?>