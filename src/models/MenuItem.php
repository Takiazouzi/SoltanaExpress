<?php declare(strict_types=1);
// ============================================================================
// MenuItem Model - Full CRUD + Image Upload
// ============================================================================
class MenuItem {
    private \PDO $db;
    public function __construct(\PDO $pdo) { $this->db = $pdo; }

    public function getAll(): array {
        return $this->db->query('SELECT * FROM menu_items ORDER BY category, name')->fetchAll();
    }

    public function create(array $data): array {
        $stmt = $this->db->prepare('INSERT INTO menu_items (name, description, price, category, available, image_path) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['name'], $data['description'], (float)$data['price'],
            $data['category'], (int)$data['available'], $data['image_path'] ?? null
        ]);
        return ['success' => true, 'id' => (int)$this->db->lastInsertId()];
    }

    public function update(int $id, array $data): array {
        $stmt = $this->db->prepare('UPDATE menu_items SET name=?, description=?, price=?, category=?, available=?, image_path=? WHERE id=?');
        $stmt->execute([
            $data['name'], $data['description'], (float)$data['price'],
            $data['category'], (int)$data['available'], $data['image_path'] ?? null, $id
        ]);
        return ['success' => true, 'rows' => $stmt->rowCount()];
    }

    public function delete(int $id): array {
        $stmt = $this->db->prepare('UPDATE menu_items SET available = 0 WHERE id = ?');
        $stmt->execute([$id]);
        return ['success' => true, 'rows' => $stmt->rowCount()];
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM menu_items WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function uploadImage(int $id, array $file, string $uploadDir): array {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error or no file selected'];
        }

        // Validate MIME type (server-side, not extension)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mime, $allowedMimes, true)) {
            return ['success' => false, 'message' => 'Invalid type. Use JPEG, PNG, or WebP.'];
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'message' => 'File exceeds 2MB limit.'];
        }

        $ext = match($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null
        };
        $filename = uniqid('item_', true) . '.' . $ext;
        $dest = rtrim($uploadDir, '/') . '/' . $filename;

        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $url = '/uploads/' . $filename;
            $this->db->prepare('UPDATE menu_items SET image_path = ? WHERE id = ?')->execute([$url, $id]);
            return ['success' => true, 'url' => $url];
        }
        return ['success' => false, 'message' => 'Failed to save image.'];
    }
}
