<?php

$logPath = __DIR__ . '/../storage/logs/laravel.log';
$content = file_get_contents($logPath);

// Find the last "Face descriptor extraction failed" entry and print only
// the first line of its error message plus the final line (which usually
// contains the actual thrown error, after the huge minified-JS context line).
$entries = preg_split('/^\[\d{4}-\d{2}-\d{2}/m', $content);
$last = trim(end($entries));

if (!$last) {
    echo "No log entries found.\n";
    exit;
}

$lines = explode("\n", $last);
echo "First line: " . ($lines[0] ?? '') . "\n";
echo "---\n";
echo "Last 5 lines:\n";
echo implode("\n", array_slice($lines, -5)) . "\n";
