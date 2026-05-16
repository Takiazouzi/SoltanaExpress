<?php declare(strict_types=1);
class User {
    private \PDO $db;
    
    public function __construct(string $dsn, string $user, string $pass) {
        $this->db = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }
    
    public function register(string $name, string $email, string $password, string $confirm): array {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email'];
        }
        if ($password !== $confirm) {
            return ['success' => false, 'message' => 'Passwords mismatch'];
        }
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password too short'];
        }
        
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email registered'];
        }
        
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 4]); // dev speed
        $stmt = $this->db->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $hash]);
        
        return ['success' => true, 'message' => 'Registered', 'user_id' => (int)$this->db->lastInsertId()];
    }
    
    public function login(string $email, string $password): array {
        $stmt = $this->db->prepare('SELECT id, name, role, password FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
        
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        
        return ['success' => true, 'message' => 'Logged in', 'redirect' => '/profile.php'];
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
}
