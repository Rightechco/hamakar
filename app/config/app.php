<?php
// app/config/app.php

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (!empty($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false);
$scheme = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'demo.hamakar.ir';
$defaultUrl = "$scheme://$host";

define('APP_URL', $_ENV['APP_URL'] ?? $defaultUrl);
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
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
