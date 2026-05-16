<?php
// Force output, no strict types, no buffering issues
error_reporting(0); // Suppress all warnings that could break JSON
ob_end_clean(); // Clear any previous output buffer
ob_start(); // Start fresh

header('Content-Type: application/json');

// Hardcoded test response
$response = [
    'test' => 'force-output',
    'php_version' => phpversion(),
    'session_status' => session_status(),
    'time' => date('c')
];

$output = json_encode($response);
echo $output;
flush();
ob_flush();
exit; // Critical: stop execution here
