<?php
// app/config/database.php

// Load .env file
// مسیر را به '../../.env' تغییر دهید تا از 'config' به 'public_html' برسد
if (file_exists(__DIR__ . '/../../.env')) {
    $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
} else {
    // این بخش برای نمایش خطا در صورتی که .env پیدا نشد اضافه شده است
    if (APP_ENV === 'development') { // اینجا از APP_ENV استفاده می شود
        die("Error: .env file not found in " . __DIR__ . "/../../.env"); // مسیر خطای جدید
    } else {
        die("Application configuration error.");
    }
}

// Database configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'winboo_comdb');
define('DB_USER', $_ENV['DB_USER'] ?? 'winboo_comdb');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'oV2&7d~O]B7&KaZo');