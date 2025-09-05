<?php
// app/core/Router.php

class Router {
    protected static $routes = [];

    public static function get($uri, $action) {
        self::$routes['GET'][self::formatUri($uri)] = $action;
    }

    public static function post($uri, $action) {
        self::$routes['POST'][self::formatUri($uri)] = $action;
    }

    // متد کمکی برای فرمت کردن URI (اضافه کردن اسلش ابتدایی)
    private static function formatUri($uri) {
        $uri = '/' . trim($uri, '/');
        return $uri;
    }

    public static function dispatch() {
        // دریافت URI درخواست شده و اطمینان از وجود اسلش ابتدایی
        $uri = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $method = $_SERVER['REQUEST_METHOD'];

        // --- شروع بخش تغییر یافته برای دیباگ / تطابق ساده ---
        // این بخش را برای حل مشکل preg_match به صورت مستقیم تری تغییر می دهیم
        if (isset(self::$routes[$method])) {
            foreach (self::$routes[$method] as $route_uri => $action) {
                // برای روت های بدون پارامتر، تطابق مستقیم string
                if ($route_uri === $uri) {
                    list($controllerName, $methodName) = explode('@', $action);
                    $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

                    if (file_exists($controllerFile)) {
                        require_once $controllerFile;
                        if (class_exists($controllerName)) {
                            $controller = new $controllerName();
                            if (method_exists($controller, $methodName)) {
                                // اگر روت بدون پارامتر است، نیازی به matches نیست
                                call_user_func([$controller, $methodName]);
                                return; // روت پیدا و اجرا شد
                            }
                        }
                    }
                }
                // اگر روت دارای پارامتر است، از preg_match استفاده می کنیم
                // این بخش فقط برای روت هایی با پارامتر فعال می شود
                $route_regex = '#^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_]+)', $route_uri) . '$#';
                if (strpos($route_uri, '{') !== false && preg_match($route_regex, $uri, $matches)) {
                    array_shift($matches); // حذف اولین عنصر (کل مطابقت)

                    list($controllerName, $methodName) = explode('@', $action);
                    $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

                    if (file_exists($controllerFile)) {
                        require_once $controllerFile;
                        if (class_exists($controllerName)) {
                            $controller = new $controllerName();
                            if (method_exists($controller, $methodName)) {
                                call_user_func_array([$controller, $methodName], $matches);
                                return; // روت پیدا و اجرا شد
                            }
                        }
                    }
                }
            }
        }
        // --- پایان بخش تغییر یافته ---


        // اگر هیچ روتی پیدا نشد، این dd() اجرا می شود (فقط در توسعه)
        if (APP_ENV === 'development') {
            dd([
                'requested_uri' => $uri,
                'request_method' => $method,
                'defined_routes' => self::$routes,
                'error_at_file' => __FILE__,
                'error_at_line' => __LINE__,
                'message' => 'No route matched the request.',
                'server_request_uri' => $_SERVER['REQUEST_URI'],
                'server_script_name' => $_SERVER['SCRIPT_NAME'] // برای بررسی baseURL
            ]);
        }

        self::handleNotFound();
    }

    protected static function handleNotFound() {
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        exit();
    }
}