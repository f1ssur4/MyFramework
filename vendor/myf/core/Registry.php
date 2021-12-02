<?php


namespace myf\core;


class Registry
{
     public static $objects = [];
     protected static $instance;

     protected function __construct()
     {
         $config = require_once 'app/config/config.php';
         foreach ($config['classes'] as $classname => $path) {
            self::$objects[$classname] = new $path;
         }
     }

     private function __clone()
     {
         //
     }

     public function __wakeup()
     {
         //
     }

     public static function instance()
     {
         if (self::$instance === null){
             self::$instance = new self();
         }
         return self::$instance;
     }

     public function __get($classname)
     {
            if (is_object(self::$objects[$classname])){
                return self::$objects[$classname];
            }
     }

     public function __set($classname, $path)
     {
            if (!isset(self::$objects[$classname])){
                self::$objects[$classname] = new $path;
            }
     }

     public function getList()
     {
            echo '<pre>';
            var_dump(self::$objects);
            echo '</pre>';
     }
}