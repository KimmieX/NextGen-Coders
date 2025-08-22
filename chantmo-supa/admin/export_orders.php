<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/Order.php';

// Check admin authentication
if (!isLoggedIn() || !isAdmin()) {
    die('Unauthorized access');
}

// Apply the same filters as orders.php
$filters = [
    'status' => $_GET['status'] ?? null,
    'payment_method' => $_GET['payment_method'] ?? null,
    'date_from' => $_GET['date_from'] ?? null,
    'date_to' => $_GET['date_to'] ?? null,
    'order_number' => $_GET['order_number'] ?? null,
    'customer' => $_GET['customer'] ?? null
];

// Clean empty filters
$filters = array_filter($filters, function($value) {
    return $value !== null && $value !== '';
});

// Get filtered orders
$orders = Order::getAll($filters);

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    // Set headers
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="orders_export_' . date('Y-m-d_H-i') . '.csv"');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fwrite($output, "\xEF\xBB\xBF");
    
    // CSV Headers
    fputcsv($output, [
        'Order ID', 'Customer', 'Date', 'Total', 'Payment Method', 'Status'
    ]);
    
    // Data Rows
    foreach ($orders as $order) {
        fputcsv($output, [
            $order['order_number'],
            $order['username'],
            date('Y-m-d H:i', strtotime($order['created_at'])),
            number_format($order['total_amount'], 2),
            ucfirst(str_replace('_', ' ', $order['payment_method'])),
            ucfirst($order['status'])
        ]);
    }
    
    fclose($output);
    exit;
}

// If no export type matched
die('Invalid export request');