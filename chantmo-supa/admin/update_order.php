<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Check admin authentication
if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

$action = $_POST['action'] ?? '';
$orderId = (int)($_POST['order_id'] ?? 0);

try {
    switch ($action) {
        case 'save_admin_notes':
    $adminNotes = trim($_POST['admin_notes'] ?? '');
    
    // Automatically prepend timestamp and admin name
    $username = $_SESSION['username'] ?? 'System';
    $timestamp = date('n/j H:i');
    $adminNotes = "[$timestamp] $username:\n" . $adminNotes . "\n\n" . 
                 ($order['admin_notes'] ?? '');
    
    // Trim to last 10 notes if needed
    $notesArray = array_slice(explode("\n\n", $adminNotes), 0, 10);
    $adminNotes = implode("\n\n", $notesArray);
    
    $stmt = $pdo->prepare("UPDATE orders SET admin_notes = ? WHERE id = ?");
    $success = $stmt->execute([$adminNotes, $orderId]);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Notes updated',
            'formatted_notes' => nl2br(htmlspecialchars($adminNotes))
        ]);
    }
    break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}