<?php declare(strict_types=1);
// ============================================================================
// User Model - Profile & Account Methods
// ============================================================================

class User {
    private \PDO $db;
    
    public function __construct(\PDO $pdo) {
        $this->db = $pdo;
    }
    
    // Existing auth methods (register, login, logout) kept minimal for brevity
    // ... [register/login/logout methods from before] ...
    
    public function getProfile(int $userId): array {
        $stmt = $this->db->prepare('SELECT id, name, email, role, avatar, created_at FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) return [];
        
        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'avatar' => $user['avatar'],
            'member_since' => date('F Y', strtotime($user['created_at'])),
            'joined_date' => $user['created_at']
        ];
    }
    
    public function updateProfile(int $userId, string $name): array {
        $name = trim($name);
        if (empty($name) || strlen($name) < 2) {
            return ['success' => false, 'message' => 'Name must be at least 2 characters.'];
        }
        
        $stmt = $this->db->prepare('UPDATE users SET name = ? WHERE id = ?');
        $stmt->execute([$name, $userId]);
        
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Update failed or no changes.'];
        }
        
        return ['success' => true, 'message' => 'Profile updated.', 'name' => $name];
    }
}
