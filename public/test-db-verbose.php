<?php declare(strict_types=1);
header('Content-Type: application/json');

// Load .env same way as db.php
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
    }
}
loadEnv(__DIR__ . '/../.env');

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'restaurant';
$user = getenv('DB_USER') ?: 'restaurant_app';
$pass = getenv('DB_PASS') ?: 'app_secret_123';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new \PDO($dsn, $user, $pass, [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]);
    echo json_encode([
        'connected' => true,
        'server_version' => $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION),
        'driver' => $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME),
        'user' => $user,
        'host' => "$host:$port"
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'connected' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
        'dsn' => $dsn,
        'user' => $user
    ]);
}
