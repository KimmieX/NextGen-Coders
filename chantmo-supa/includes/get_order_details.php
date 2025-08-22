<?php
// Absolute path to config.php
require_once __DIR__ . '/config.php';

// Make sure to include the Order class
require_once __DIR__ . '/Order.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers FIRST to prevent any output
header('Content-Type: application/json');

try {
    // Check authentication
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access', 401);
    }

    $orderId = (int)($_GET['order_id'] ?? 0);
    $userId = (int)$_SESSION['user_id'];

    if ($orderId <= 0) {
        throw new Exception('Invalid Order ID', 400);
    }

    // Get order with user verification
    $order = Order::getOrderWithItems($orderId, $userId);

    if (!$order) {
        throw new Exception('Order not found', 404);
    }

    // Prepare response
    $response = [
    'order' => [
        'id' => $order['id'],
        'order_number' => $order['order_number'],
        'total_amount' => (float)$order['total_amount'],
        'payment_method' => $order['payment_method'],
        'payment_status' => $order['payment_status'],
        'status' => $order['status'],
        'address' => $order['address'] ?? 'Not provided',
        'phone' => $order['phone'] ?? 'Not provided',
        'created_at' => $order['created_at'],
        'email' => $order['email'] ?? '',
        'username' => $order['username'] ?? ''
    ],
    'items' => []
];

foreach ($order['items'] as $item) {
    $response['items'][] = [
        'name' => $item['name'],
        'price' => (float)$item['price'],
        'quantity' => (int)$item['quantity'],
        'image_url' => $item['image_url'] ?? '',
        'original_price' => (float)($item['original_price'] ?? $item['price'])
    ];
}

    echo json_encode($response);
    exit;
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
    exit;
}