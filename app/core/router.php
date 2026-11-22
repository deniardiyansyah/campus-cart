<?php

class Router
{
    private array $routes = [];
    private array $groupStack = [];

    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function group(array $options, callable $callback): void
    {
        $this->groupStack[] = [
            'prefix'     => $options['prefix'] ?? '',
            'middleware' => $options['middleware'] ?? [],
        ];
        $callback($this);
        array_pop($this->groupStack);
    }

    private function add(string $method, string $path, $handler, array $middleware): void
    {
        $prefix = '';
        foreach ($this->groupStack as $g) {
            $prefix .= '/' . trim($g['prefix'], '/');
            $middleware = [...$g['middleware'], ...$middleware];
        }
        $full = '/' . trim($prefix . '/' . trim($path, '/'), '/');
        $full = preg_replace('#/+#', '/', $full);

        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $full);

        $this->routes[] = [
            'method'     => $method,
            'regex'      => '#^' . $regex . '$#',
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // dukung akses lewat subfolder (mis. localhost/campuscart/public)
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        $uri = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['regex'], $uri, $m)) {
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);

                foreach ($route['middleware'] as $mw) {
                    $this->runMiddleware($mw);
                }
                $this->callHandler($route['handler'], array_values($params));
                return;
            }
        }
        abort(404);
    }

    private function runMiddleware(string $name): void
    {
        [$key, $args] = array_pad(explode(':', $name, 2), 2, '');
        $args = $args === '' ? [] : explode(',', $args);

        switch ($key) {
            case 'auth':
                AuthMiddleware::handle();
                break;
            case 'role':
                RoleMiddleware::handle($args);
                break;
            case 'csrf':
                CsrfMiddleware::handle();
                break;
            default:
                throw new RuntimeException("Middleware '{$key}' tidak dikenal");
        }
    }

    private function callHandler($handler, array $params): void
    {
        if (is_callable($handler)) {
            $handler(...$params);
            return;
        }

        // format: 'HomeController@index' atau 'Seller\ProductController@index'
        [$class, $action] = explode('@', $handler);
        if (str_contains($class, '\\')) {
            $class = 'App\\Controllers\\' . $class;
        }
        if (!class_exists($class) || !method_exists($class, $action)) {
            throw new RuntimeException("Handler {$handler} tidak ditemukan");
        }
        (new $class())->$action(...$params);
    }
}
