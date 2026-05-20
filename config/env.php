<?php declare(strict_types=1);
// ============================================================================
// Robust Environment Variable Loader
// Manually parses .env file to ensure compatibility with all PHP setups
// ============================================================================
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, "\"' ");
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
