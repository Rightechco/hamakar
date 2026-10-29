<?php
// app/config/database.php

// Load .env file
if (file_exists(__DIR__ . '/../../.env')) {
    $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '' && strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            // remove inline comments if any
            if (strpos($value, '#') !== false) {
                $value = trim(explode('#', $value)[0]);
            }
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Database configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'hamakar_hamdbb');
define('DB_USER', $_ENV['DB_USER'] ?? 'hamakar_hamdbb');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'EMyCP[Pk]JfX@n)4');
