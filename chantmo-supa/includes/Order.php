<?php
class Order {
    public static function getAll($filters = []) {
    global $pdo;
    
    $sql = "SELECT o.*, u.username, u.email 
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.deleted_at IS NULL";
    
    $params = [];
    
    // Status filter
    if (!empty($filters['status'])) {
        $sql .= " AND o.status = :status";
        $params[':status'] = $filters['status'];
    }
    
    // Payment method filter
    if (!empty($filters['payment_method'])) {
        $sql .= " AND o.payment_method = :payment_method";
        $params[':payment_method'] = $filters['payment_method'];
    }
    
    // Date range filter
    if (!empty($filters['date_from'])) {
        $sql .= " AND DATE(o.created_at) >= :date_from";
        $params[':date_from'] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $sql .= " AND DATE(o.created_at) <= :date_to";
        $params[':date_to'] = $filters['date_to'];
    }
    
    // Order number filter
    if (!empty($filters['order_number'])) {
        $sql .= " AND o.order_number LIKE :order_number";
        $params[':order_number'] = '%' . $filters['order_number'] . '%';
    }
    
    // Customer filter - fixed to use two distinct parameters
    if (!empty($filters['customer'])) {
        $sql .= " AND (u.username LIKE :customer_username OR u.email LIKE :customer_email)";
        $params[':customer_username'] = '%' . $filters['customer'] . '%';
        $params[':customer_email'] = '%' . $filters['customer'] . '%';
    }
    
    $sql .= " ORDER BY o.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

    public static function getOrderWithItems($orderId, $userId = null) {
    global $pdo;
    
    $sql = "SELECT o.*, u.username, u.email 
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ?";
    
    if ($userId !== null) {
        $sql .= " AND o.user_id = ?";
    }
    
    $stmt = $pdo->prepare($sql);
    $params = $userId !== null ? [$orderId, $userId] : [$orderId];
    $stmt->execute($params);
    $order = $stmt->fetch();
    
    if (!$order) {
        return null;
    }
    
    // Get order items with product details
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.image_url, p.price as original_price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $order['items'] = $stmt->fetchAll();
    
    return $order;
}

    public static function updateStatus($orderId, $status) {
        global $pdo;
        $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $stmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $orderId]);
    }

    public static function delete($orderId) {
        global $pdo;
        
        try {
            $pdo->beginTransaction();
            
            // First delete order items
            $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->execute([$orderId]);
            
            // Then delete the order
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);
            
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Order deletion error: " . $e->getMessage());
            return false;
        }
    }

    public static function count($conditions = []) {
    global $pdo;
    
    $sql = "SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL";
    $params = [];
    
    if (!empty($conditions)) {
        foreach ($conditions as $field => $value) {
            // Handle special cases like DATE()
            if (strpos($field, 'DATE(') === 0) {
                $sql .= " AND $field = ?";
            } else {
                $sql .= " AND $field = ?";
            }
            $params[] = $value;
        }
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

    public static function getUserOrders($userId, $limit = null) {
    global $pdo;
    
    $sql = "SELECT * FROM orders 
            WHERE user_id = ? AND deleted_at IS NULL
            ORDER BY created_at DESC";
            
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

public static function getOrderNotes($orderId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT n.*, a.username 
                          FROM order_notes n
                          JOIN admins a ON n.admin_id = a.id
                          WHERE order_id = ?
                          ORDER BY created_at DESC");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll();
}

public static function updatePaymentStatus($orderId, $status) {
    global $pdo;
    $validStatuses = ['pending', 'paid'];
    
    if (!in_array($status, $validStatuses)) {
        return false;
    }
    
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = ?, updated_at = NOW() WHERE id = ?");
    return $stmt->execute([$status, $orderId]);
}
}
?>