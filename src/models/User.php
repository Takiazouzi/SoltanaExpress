<?php declare(strict_types=1);
// ============================================================================
// User Model - Minimal (Hardcoded DB for Dev)
// ============================================================================

class User {
    private \PDO $db;
    
    // Constructor accepts PDO to avoid config loading issues
    public function __construct(\PDO $pdo) {
        $this->db = $pdo;
    }
    
    public function register(string $name, string $email, string $password, string $confirm): array {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format.'];
        }
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
        }
        if ($password !== $confirm) {
            return ['success' => false, 'message' => 'Passwords do not match.'];
        }
        
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email already registered.'];
        }
        
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 4]); // cost 4 for dev speed
        $stmt = $this->db->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $hash]);
        
        return ['success' => true, 'message' => 'Account created successfully.', 'user_id' => (int)$this->db->lastInsertId()];
    }
    
    public function login(string $email, string $password): array {
        $stmt = $this->db->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }
        
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        
        return ['success' => true, 'message' => 'Login successful.', 'redirect' => '/profile.php'];
    }
    
    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
    
    public static function getCurrentUser(\PDO $db): ?array {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user_id'])) return null;
        
        $stmt = $db->prepare('SELECT id, name, email, role, avatar, created_at FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch() ?: null;
    }
}
