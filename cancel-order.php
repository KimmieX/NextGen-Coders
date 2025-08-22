<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/Order.php';

if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/chantmo-supa/index.php');
}

$orderId = $_POST['order_id'] ?? 0;
$reason = trim($_POST['reason'] ?? '');

// Verify the order belongs to the user
$order = Order::getOrderWithItems($orderId, $_SESSION['user_id']);

if (!$order || $order['status'] !== 'pending') {
    $_SESSION['error'] = 'Cannot cancel this order';
    redirect('/chantmo-supa/pages/orders.php');
}

if (empty($reason)) {
    $_SESSION['error'] = 'Please provide a cancellation reason';
    redirect('/chantmo-supa/pages/order-details.php?id=' . $orderId);
}

// Update order status to cancelled
if (Order::updateStatus($orderId, 'cancelled')) {
    // Here you could also send an email notification to admin
    $_SESSION['success'] = 'Order cancellation requested successfully';
} else {
    $_SESSION['error'] = 'Failed to cancel order';
}

redirect('/chantmo-supa/pages/order-details.php?id=' . $orderId);