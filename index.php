<?php
namespace app;
use myf\core\Router;


require_once $_SERVER['DOCUMENT_ROOT'] . '/app/config/db.php';

define('DEBUG', 1);

require 'vendor/autoload.php';

new \myf\lib\ErrorsHandler;
new \myf\core\App;

session_start();

$router = new Router();
$router->addPath();
?>