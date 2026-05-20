<?php declare(strict_types=1);
// ============================================================================
# Environment Variable Loader
# Reads .env file and populates getenv(), $_ENV, $_SERVER
# ============================================================================

$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Strip surrounding quotes
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || 
            (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// Validate required database variables
$requiredDbVars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($requiredDbVars as $var) {
    if (empty(getenv($var))) {
        error_log("Environment error: Missing required variable $var");
        if (getenv('APP_ENV') === 'development') {
            // Fail fast in dev, hide details in prod
            throw new \RuntimeException("Missing required env var: $var");
        }
    }
}
