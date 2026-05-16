<?php
header('Content-Type: text/plain');
$log = __DIR__ . '/../logs/test.log';
$written = @file_put_contents($log, "Test at " . date('c') . "\n", FILE_APPEND);
echo "Log path: $log\n";
echo "Write result: " . ($written ? "SUCCESS ($written bytes)" : "FAILED") . "\n";
echo "File exists: " . (file_exists($log) ? "YES" : "NO") . "\n";
if (file_exists($log)) {
    echo "Log contents:\n" . file_get_contents($log);
}
