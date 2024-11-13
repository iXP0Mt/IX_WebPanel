<?php

namespace routes;

use app\helpers\Middleware_Auth;

class Router
{
    private $base;

    public function __construct($base)
    {
        $this->base = $base;
    }

    private array $routes = [];


    public function get($route, $controllerName, $actionName)
    {
        $this->addRoute('GET', $route, $controllerName, $actionName);
    }

    public function post($route, $controllerName, $actionName)
    {
        $this->addRoute('POST', $route, $controllerName, $actionName);
    }


    public function addRoute($method, $route, $controllerName, $actionName)
    {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'controller' => $controllerName,
            'action' => $actionName
        ];
    }


    public function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {

            // Проверяем путь и заполняем параметры
            if(!$this->matchRoute($route['route'], $uri, $params)) {
                continue;
            }

            // Проверяем метод запроса.
            if($route['method'] !== $method) {
                continue;
            }

            // Проверяем доступ пользователя
            Middleware_Auth::checkAccess($route['route']);

            // Проверяем контроллер
            $controllerPath = $this->base.'/controllers/'.$route['controller'].'.php';
            if(!file_exists($controllerPath)) {
                echo "ФАЙЛ НЕ СУЩЕСТВУЕТ";
                die;
            }

            // Подгружаем контроллер
            include_once $controllerPath;
            if(!class_exists($route['controller'], false)) {
                echo "КЛАСС НЕ СУЩЕСТВУЕТ";
                die;
            }

            // Подгружаем модель
            $modelPath = $this->base.'/models/'.
                str_replace('controller_', 'model_', strtolower($route['controller']))
                .'.php';

            if(file_exists($modelPath)) {
                include $modelPath;
            }


            // Вызываем действие
            $controllerClass = new $route['controller']();
            if(!method_exists($controllerClass, $route['action'])) {
                echo "МЕТОД НЕ СУЩЕСТВУЕТ";
                die;
            }

            if(call_user_func_array([$controllerClass, $route['action']], $params) === false) {
                echo "ОШИБКА ВЫЗОВА МЕТОДА";
                die;
            }
            return;
        }

        echo 'МАРШРУТ НЕ НАЙДЕН';
    }


    private function matchRoute($routePattern, $uri, &$params): bool
    {
        $params = [];

        preg_match_all('/:(\w+)/', $routePattern, $paramNames);
        $paramNames = $paramNames[1];

        $isWildcardEnd = str_ends_with($routePattern, '/...');

        $isWildcardStart = str_starts_with($routePattern, '.../');

        if ($isWildcardEnd) {
            $routePattern = substr($routePattern, 0, -4);
        }

        if($isWildcardStart) {
            $routePattern = substr($routePattern, 4);
        }

        $pattern = preg_replace('/:(\w+)/', '(?P<$1>[^/]+)', $routePattern);

        if ($isWildcardEnd) {
            $pattern .= '(/.*)?';
        }

        if($isWildcardStart) {
            $pattern = '(.+/)?' . $pattern;
        }

        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            foreach ($paramNames as $name) {
                if (isset($matches[$name])) {
                    $params[$name] = $matches[$name];
                }
            }
            return true;
        }

        return false;
    }
}