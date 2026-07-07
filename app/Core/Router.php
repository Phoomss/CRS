<?php

namespace App\Core;

class Router {
    protected array $routes = [];

    /**
     * Register a GET route.
     */
    public function get(string $path, array|callable $handler, array $middlewares = []): void {
        $this->add('GET', $path, $handler, $middlewares);
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, array|callable $handler, array $middlewares = []): void {
        $this->add('POST', $path, $handler, $middlewares);
    }

    /**
     * Add a route with custom method.
     */
    public function add(string $method, string $path, array|callable $handler, array $middlewares = []): void {
        // Convert path curly brackets to regex groups, e.g. /computers/{id} => /computers/([^/]+)
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $path);
        $pattern = '#^' . trim($pattern, '/') . '$#';

        $this->routes[] = [
            'method'      => strtoupper($method),
            'pattern'     => $pattern,
            'handler'     => $handler,
            'middlewares' => $middlewares,
            'original'    => $path
        ];
    }

    /**
     * Resolve the incoming request path against registered routes.
     */
    public function resolve(Request $request): void {
        $path = trim($request->getPath(), '/');
        $method = $request->getMethod();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                array_shift($matches); // Remove full match

                // Execute middlewares
                foreach ($route['middlewares'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    if (!$middleware->handle($request)) {
                        return; // Stopped by middleware
                    }
                }

                $handler = $route['handler'];

                if (is_callable($handler)) {
                    call_user_func_array($handler, array_merge([$request], $matches));
                    return;
                }

                if (is_array($handler)) {
                    [$controllerClass, $action] = $handler;
                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass();
                        if (method_exists($controller, $action)) {
                            call_user_func_array([$controller, $action], array_merge([$request], $matches));
                            return;
                        }
                    }
                }

                // If handler is broken
                $this->sendError(500, "Internal Server Error: Handler not found.");
                return;
            }
        }

        // 404 Route Not Found
        $this->sendError(404, "Page Not Found");
    }

    /**
     * Send error view or JSON depending on request type.
     */
    private function sendError(int $code, string $message): void {
        Response::setStatusCode($code);
        $request = new Request();
        if ($request->isAjax()) {
            Response::json(['error' => $message], $code);
        } else {
            // Render basic HTML error page or standard layout error
            $viewPath = __DIR__ . "/../../views/errors/{$code}.php";
            if (file_exists($viewPath)) {
                require_once $viewPath;
            } else {
                echo "<h1 style='text-align:center; margin-top:50px;'>Error {$code}: {$message}</h1>";
            }
        }
        exit;
    }
}
