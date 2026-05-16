<?php declare(strict_types=1);
// ============================================================================
// Database Connection (PDO Singleton)
// ============================================================================

// Simple .env loader (no external deps)
function loadEnv(string $path): void {
    if (!file_exists($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key); $value = trim($value);
        if ($value && str_starts_with($value, '"') && str_ends_with($value, '"')) {
            $value = substr($value, 1, -1);
        }
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}
loadEnv(__DIR__ . '/../../.env');

class Database {
    private static ?self $instance = null;
    private \PDO $conn;

    private function __construct() {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $dbname = getenv('DB_NAME') ?: 'restaurant';
        $user = getenv('DB_USER') ?: 'restaurant_app';
        $pass = getenv('DB_PASS') ?: 'app_secret_123';
        
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $opts = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
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
