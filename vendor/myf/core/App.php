<?php

namespace myf\core;
use myf\core\Registry;

class App
{
    public static $app;
    public function __construct()
    {
        self::$app = Registry::instance();
    }
    public static function redirect($url = null)
    {
        if ($url === null){
            header('location: ' .'/'. $url);
        }else{
            $route = require 'app/config/routes.php';
            foreach ($route as $key => $value) {
                if ($key === $url){
                    header('location: /' . $key);
                    break;
                }
            }

        }
    }

    public static function isPost()
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
            return true;
        }else{
            return false;
        }
    }
}