<?php declare(strict_types=1);
// ============================================================================
// MenuItem Model
// ============================================================================

class MenuItem {
    private \PDO $db;

    public function __construct(\PDO $pdo) {
        $this->db = $pdo;
    }

    public function getAll(): array {
        $stmt = $this->db->query('SELECT * FROM menu_items WHERE available = 1 ORDER BY category, name');
        return $stmt->fetchAll();
    }

    public function getByCategory(string $category): array {
        $stmt = $this->db->prepare('SELECT * FROM menu_items WHERE category = ? AND available = 1 ORDER BY name');
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }

    public function getCategories(): array {
        $stmt = $this->db->query('SELECT DISTINCT category FROM menu_items ORDER BY category');
        return array_column($stmt->fetchAll(), 'category');
    }
}
