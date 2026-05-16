<?php declare(strict_types=1);
// ============================================================================
// Application Constants
// ============================================================================

// Base Configuration
define('APP_URL', rtrim(getenv('APP_URL') ?: 'http://localhost:8000', '/'));
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('MAX_FILE_SIZE', 2097152); // 2MB in bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// Menu Category Slugs (drives CSS badge color-coding)
// CSS classes: .badge-starters (amber), .badge-mains (teal), .badge-desserts (pink)
define('MENU_CATEGORY_STARTERS', 'starters');
define('MENU_CATEGORY_MAINS', 'mains');
define('MENU_CATEGORY_DESSERTS', 'desserts');

// Mapping for display/rendering vs URL/CSS slugs
define('MENU_CATEGORIES', [
    'starters' => 'Starters',
    'mains'    => 'Mains',
    'desserts' => 'Desserts',
]);
