<?php
// app/core/FlashMessage.php

class FlashMessage {
    public static function set($name, $message, $type = 'success') {
        $_SESSION['flash_' . $name] = [
            'message' => $message,
            'type' => $type
        ];
    }

    public static function get($name) {
        if (isset($_SESSION['flash_' . $name])) {
            $flash = $_SESSION['flash_' . $name];
            unset($_SESSION['flash_' . $name]); // Clear after reading
            return $flash;
        }
        return null;
    }

    public static function display() {
        $flash = self::get('message'); // Assuming 'message' is the common flash name
        if ($flash) {
            $alertClass = ($flash['type'] === 'success') ? 'alert-success' : 'alert-danger';
            echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
            echo sanitize($flash['message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
    }
}