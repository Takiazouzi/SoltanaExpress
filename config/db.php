<?php declare(strict_types=1);
// ============================================================================
// Database Connection (PDO Singleton)
// ============================================================================

class Database {
    private static ?self $instance = null;
    private \PDO $conn;

    private function __construct() {
        $dsn = 'mysql:host=localhost;dbname=restaurant;charset=utf8mb4';
        $opts = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        // Credentials should ideally come from .env, falling back to defaults
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $this->conn = new \PDO($dsn, $user, $pass, $opts);
    }

    public static function getInstance(): self {
        return self::$instance ??= new self();
    }

    public function connection(): \PDO {
        return $this->conn;
    }

    private function __clone() {}
    public function __wakeup(): void {
        throw new \RuntimeException('Unserialization not allowed.');
    }
}
