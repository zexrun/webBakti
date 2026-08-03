<?php

$logPath = __DIR__ . '/../storage/logs/laravel.log';
$content = file_get_contents($logPath);

// The face-api.node.js source is minified onto ONE line, so a Node stack
// trace that points into it embeds that entire (tens-of-thousands-of-chars)
// line inline. Strip anything that looks like minified JS (long runs with
// no spaces around common minifier tokens) before splitting into lines, so
// what's left is just the actual prose error message / real stack frames.
$content = preg_replace('/\/var\/www\/webbakti\/node_modules\/[^\n]{200,}/', '[[minified source line stripped]]', $content);

$lines = array_filter(explode("\n", trim($content)));
$lines = array_values($lines);

echo "Total lines after stripping: " . count($lines) . "\n";
echo "---\n";
echo implode("\n", array_slice($lines, -15)) . "\n";
