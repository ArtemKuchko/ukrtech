<?php

class Router {
    // Хранит конфигурацию маршрутов.
    private $routes;

    function __construct($routesPath){
        // Получаем конфигурацию из файла.
        $this->routes = include($routesPath);
    }

    // Метод получает URI. Несколько вариантов представлены для надёжности.
    function getURI(){
        if(!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }

        if(!empty($_SERVER['PATH_INFO'])) {
            return trim($_SERVER['PATH_INFO'], '/');
        }

        if(!empty($_SERVER['QUERY_STRING'])) {
            return trim($_SERVER['QUERY_STRING'], '/');
        }
    }

    function run(){
        // Получаем URI.
        $uri = $this->getURI();
        // Пытаемся применить к нему правила из конфигуации.

        foreach($this->routes as $route){
            // Если правило совпало.
            if(preg_match("~$route~", $uri)){
                // Получаем внутренний путь из внешнего согласно правилу.
                $internalRoute = preg_replace("~$route~", $route, $uri);
                // Разбиваем внутренний путь на сегменты.
                $segments = explode('/', $internalRoute);
                //1 сегмент папка
                $folder = ucfirst(array_shift($segments));
                //2 сегмент - контроллер
                $controller = ucfirst(array_shift($segments)).'Controller';
                $action = array_shift($segments);

                // Остальные сегменты — параметры.
                $params = $segments;

                // Подключаем файл контроллера, если он имеется
                $controllerFile = ROOT.'/modules/'.$folder.'/controllers/'.$controller.'.php';

                if(file_exists($controllerFile)){
                    include($controllerFile);
                }
                // Если не загружен нужный класс контроллера или в нём нет
                // нужного метода — 404
                if(!is_callable(array($controller, $action))){
                    header("HTTP/1.0 404 Not Found");
                    return;
                }
                // Вызываем действие контроллера с параметрами
                call_user_func_array(array($controller, $action), $params);
            }
        }
        return;

    }
}