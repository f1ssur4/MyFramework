<?php

namespace myf\lib\database;

use PDO;

abstract class Connect{

    private static $db;

    public static function connect()
    {
        self::$db = require 'app/config/db.php';
        return new PDO(self::$db['dsn'], self::$db['user_name'], self::$db['password'], self::$db['options']);
    }
}
?>