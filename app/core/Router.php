<?php
/**
 * Router - URL maršrutēšanas klase
 * Vienkāršs un efektīvs router bez liekām atkarībām
 */

class Router {
    private $routes = [];
    private $namedRoutes = [];

    public function add($method, $path, $handler, $name = null) {
        $pattern = $this->convertPathToPattern($path);
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'params' => $this->extractParamNames($path)
        ];

        if ($name !== null) {
            $this->namedRoutes[$name] = $path;
        }
    }

    public function get($path, $handler, $name = null) {
        $this->add('GET', $path, $handler, $name);
    }

    public function post($path, $handler, $name = null) {
        $this->add('POST', $path, $handler, $name);
    }

    public function dispatch($uri, $method = 'GET') {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // Noņemt pilno match

                $params = [];
                foreach ($route['params'] as $index => $paramName) {
                    $params[$paramName] = $matches[$index] ?? null;
                }

                return $this->callHandler($route['handler'], $params);
            }
        }

        // 404 - Nav atrasts
        http_response_code(404);
        return $this->callHandler('ErrorController@notFound', []);
    }

    private function callHandler($handler, $params = []) {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$controller, $method] = explode('@', $handler);
            $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';

            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controllerInstance = new $controller();

                if (method_exists($controllerInstance, $method)) {
                    return call_user_func_array([$controllerInstance, $method], $params);
                }
            }
        }

        throw new Exception("Kontrolieris vai metode nav atrasta: " . $handler);
    }

    private function convertPathToPattern($path) {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function extractParamNames($path) {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $path, $matches);
        return $matches[1];
    }

    public function url($name, $params = []) {
        if (!isset($this->namedRoutes[$name])) {
            return '/';
        }

        $path = $this->namedRoutes[$name];
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }

        return $path;
    }
}
