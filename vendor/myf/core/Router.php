<?php
namespace myf\core;
use myf\core\Controller;
use myf\lib\PageNotFoundException;

class Router
{
    public function runController($controller, $action, $route)
    {
        $class = 'app\controller\\' . ucfirst($controller) . 'Controller';
        $actionClass =  'action' . ucfirst($action);
        if (class_exists($class)){
            if (method_exists($class, $actionClass)){
                $obj = new $class();
                $obj->controller = $controller;
                $obj->action = $action;
                echo $obj->$actionClass();
            }else{
                throw new \Exception('action not found');
            }
        }else{
            throw new \Exception('controller not found');
        }
    }


    public function addPath()
    {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        $this->match($url);
    }

    public function match($url)
    {
        $routes = require 'app/config/routes.php';
        $controller = null;
        $action = null;

        foreach ($routes as $route => $params) {

            if ($route == $url){
                $controller = $params['controller'];
                $action = $params['action'];
                return $this->runController($controller, $action, $route);
            }
        }
        throw new PageNotFoundException('The path does not exist');
    }
}