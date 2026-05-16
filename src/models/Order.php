<?php declare(strict_types=1);
// ============================================================================
// Order Model - User Order History & Details
// ============================================================================

class Order {
    private \PDO $db;
    
    public function __construct(\PDO $pdo) {
        $this->db = $pdo;
    }
    
    public function getByUser(int $userId): array {
        $stmt = $this->db->prepare('
            SELECT o.id, o.status, o.total, o.created_at, COUNT(oi.id) as items_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
            LIMIT 20
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getOrderDetail(int $orderId, int $userId): array {
        // Verify order belongs to user
        $stmt = $this->db->prepare('SELECT id, status, total, notes, created_at FROM orders WHERE id = ? AND user_id = ?');
        $stmt->execute([$orderId, $userId]);
        $order = $stmt->fetch();
        
        if (!$order) return [];
        
        // Get order items
        $stmt = $this->db->prepare('
            SELECT oi.quantity, oi.unit_price, m.name, m.image_path, m.category
            FROM order_items oi
            JOIN menu_items m ON oi.menu_item_id = m.id
            WHERE oi.order_id = ?
        ');
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll();
        
        return [
            'id' => $order['id'],
            'status' => $order['status'],
            'total' => $order['total'],
            'notes' => $order['notes'],
            'created_at' => $order['created_at'],
            'items' => $items,
            'items_count' => count($items)
        ];
    }
}
