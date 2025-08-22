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

    if (!isset($_POST['product_id'])) {
        throw new Exception('Product ID is required');
    }

    $productId = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($productId <= 0 || $quantity <= 0) {
        throw new Exception('Invalid product or quantity');
    }

    $product = Product::getById($productId);
    if (!$product) {
        throw new Exception('Product not found. It may have been removed.');
    }

    // Get current cart quantity for this product
    $currentCartQty = Cart::getCartItems()[$productId] ?? 0;
    $newQty = $currentCartQty + $quantity;

    // Check stock before adding
    if ($newQty > $product->stock_quantity) {
        throw new Exception("Sorry, only {$product->stock_quantity} available in stock");
    }

    if (!Cart::addItem($productId, $quantity)) {
        throw new Exception('Product out of stock or not found');
    }

    $cartDetails = Cart::getCartDetails();
    
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart!',
        'totalItems' => $cartDetails['count'],
        'totalPrice' => $cartDetails['total'],
        'stock' => $product->stock_quantity,
        'currentQuantity' => $newQty
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

exit;
?>