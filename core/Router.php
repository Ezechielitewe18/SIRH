<?php
class Router {
    private $routes = [];
    private $notFoundHandler;

    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function notFound($handler) {
        $this->notFoundHandler = $handler;
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/');

        if ($method === 'POST') {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $exempt = strpos($path, 'api.php') !== false
                   || strpos($path, 'cartes/checkin') !== false
                   || strpos($path, 'cartes/pointer') !== false;
            if (!$exempt && function_exists('csrf_verify') && !csrf_verify()) {
                http_response_code(403);
                die("Erreur de sécurité : token CSRF invalide. Veuillez réessayer.");
            }
        }

        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath === '\\' || $basePath === '/') {
            $basePath = '';
        }
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        if (empty($uri)) {
            $uri = '/';
        }

        if (isset($this->routes[$method][$uri])) {
            return $this->call($this->routes[$method][$uri]);
        }

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $handler) {
                $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
                $pattern = '#^' . $pattern . '$#';

                if (preg_match($pattern, $uri, $matches)) {
                    return $this->call($handler, $matches);
                }
            }
        }

        if ($this->notFoundHandler) {
            return $this->call($this->notFoundHandler);
        }

        http_response_code(404);
        echo "Page non trouvée";
    }

    private function call($handler, $params = []) {
        $named = [];
        foreach ($params as $k => $v) {
            if (is_string($k)) {
                $named[$k] = $v;
            }
        }

        if (is_array($handler)) {
            $controller = new $handler[0]();
            $method = $handler[1];
            return call_user_func_array([$controller, $method], $named);
        }
        return call_user_func_array($handler, $named);
    }
}
