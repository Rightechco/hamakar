<?php
// app/config/app.php

define('APP_URL', $_ENV['APP_URL'] ?? 'https://winbo.store');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development'); // اینجا تعریف شده
define('APP_SECRET', $_ENV['APP_SECRET'] ?? 'your_super_secret_key_here_for_sessions');

// Error reporting based on environment
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}