<?php
namespace myf\lib\database;


class Db
{
      protected $conn;
      protected static $instance;

      private function __construct()
      {
          $this->conn = Connect::connect();
      }


      public static function instance()
      {
          if (self::$instance === null){
              self::$instance = new self();
          }
          return self::$instance;
      }


      public function __wakeup()
      {
          throw new \Exception("Cannot unserialize a singleton.");
      }


      private function __clone()
      {
          //
      }


      public function execute(string $sql, array $params = [])
      {

          $stmt = $this->conn->prepare($sql);

          $res = $stmt->execute($params);

          if ($res){
              return $stmt->fetchAll();
          }else{
              return false;
          }
      }


      public function query(string $sql, array $params = [])
      {
          $stmt = $this->conn->prepare($sql);

          $res = $stmt->execute($params);

          if ($res){
               return true;
          }else{
              return false;
          }
      }


      public function run(string $sql, array $params = [])
      {
          $stmt = $this->conn->prepare($sql);

          $res = $stmt->execute($params);

          if ($res){
              return $stmt;
          }else{
              return false;
          }
      }
}

?>