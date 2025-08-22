<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/Order.php';

header('Content-Type: application/json');

// Check admin authentication
if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$orderId = $_GET['order_id'] ?? 0;
$order = Order::getOrderWithItems($orderId);

if (!$order) {
    echo json_encode(['error' => 'Order not found']);
    exit;
}

// Add admin_notes if your orders table has this column
$order['admin_notes'] = $order['admin_notes'] ?? '';

echo json_encode([
    'order' => $order,
    'items' => $order['items'] ?? []
]);