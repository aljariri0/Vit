<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$file = realpath(__DIR__ . rawurldecode($path));
// Encoded spaces are common in catalog image paths (for example, Omega and Fish Oil).
if ($path !== '/' && $file !== false
    && str_starts_with($file, __DIR__ . DIRECTORY_SEPARATOR)
    && is_file($file)) return false;
require __DIR__ . '/index.php';
