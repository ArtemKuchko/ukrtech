<?php

// подключаем необходимые файлы
define('ROOT', dirname(__FILE__));
require_once(ROOT.'/Router.php');

// подключаем конфигурацию URL
$routes=ROOT.'/routes.php';

// запускаем роутер
$router = new Router($routes);
$router->run();