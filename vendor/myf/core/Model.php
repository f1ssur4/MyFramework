<?php

namespace myf\core;


use myf\lib\database\Db;
use Valitron\Validator;

abstract class Model
{
    protected $fetchMode = \PDO::FETCH_ASSOC;
    private   $sql;
    private   $params;
    public    $conn;
    protected $table;
    public $attributes = [];
    public $rules = [];
    public $uniqueErrors;
    public $errorsLogin;



    public function login()
    {
        if ($this->load()){
            $one = $this->select('*', ['where' => $this->attributes]);
            if (!empty($one)){
                    $_SESSION['user_data']['username'] = $one[0]['username'];
                    $_SESSION['user_data']['email'] = $one[0]['email'];
                    $_SESSION['auth'] = 1;
                    return true;
            }else{
                $this->errorsLogin = 'You made a mistake while typing or the user does not exist.';
                return false;
            }
        }
    }

    public function autocompleteAttributes()
    {
        foreach ($_POST as $key => $value) {
            $this->attributes[$key] = '';
        }

    }
    public function load()
    {
        foreach ($_POST as $field => $value) {
            if (isset($this->attributes[$field])){
                $this->attributes[$field] = $_POST[$field];
            }
        }
        return true;
    }

    public function validate()
    {
        $v = new Validator($this->attributes);
        $v->rules($this->rules);
        if($v->validate()){
            return true;
        }
        $this->uniqueErrors = $v->errors();
        return false;
    }

    public function save()
    {
        $this->insert($this->attributes);
        return true;
    }


    public function unique(array $uniqueColumn)
    {
        foreach ($uniqueColumn as $column) {
            $one = $this->select("$column", ['where' => ["$column" => $this->attributes["$column"]],'limit' => [1]]);
            if (!empty($one) && $one[0]["$column"] === $this->attributes["$column"]) {
                $this->uniqueErrors =  $column;
                return false;
            }
        }
        return true;
    }

    public function getErrors()
    {
        $errors = '<ul>';
        foreach ($this->uniqueErrors as $ArrError) {
            foreach ($ArrError as $error) {
                $errors .= '<li>' . $error . '</li>';
            }
        }
        $errors .= '</ul>';
        return $errors;
    }

    public function __construct()
    {
        $this->conn = Db::instance();
    }

    public function insert(array $params)
    {
        $newParams = [];
        $columns = '(';
        $values = '(';
        $i = 1;
        foreach ($params as $key => $value) {

            if ($i == count($params)){
                $columns .= $key . ')';
                $values .= '?)';
                $newParams[] = $value;
                break;
            }
            $columns .= $key . ',';
            $values .= '?,';
            $newParams[] = $value;
            $i++;
        }

        $sql = "INSERT INTO {$this->table} {$columns} VALUES {$values}";
        return $this->conn->query($sql, $newParams);

    }


    public function select(string $select, array $params = [])
    {
        if (empty($params)){
            $sql = "SELECT {$select} FROM {$this->table}";
            return $this->conn->execute($sql);
        }else{
            $sql = "SELECT {$select} FROM {$this->table}";
           return $this->helper($sql, $params);
        }
    }


    public function update(array $set, array $where)
    {
        $newParams[] = $set[key($set)];
        $newParams[] = $where[key($where)];
        $sql = "UPDATE {$this->table} SET " . key($set) . " = ?" . " WHERE " . key($where) . " = ?" ;

        return $this->conn->query($sql, $newParams);
    }


    public function delete(array $where)
    {
        $newParams[] = $where[key($where)];
        $sql = "DELETE FROM {$this->table} WHERE " . key($where) . " = ?" ;

        return $this->conn->query($sql, $newParams);
    }


    public function helper(string $sql, array $params)
    {
        $newParams = [];
        $add = ' WHERE ';
//            if (array_key_exists('like', $params)){
//                foreach ($params['like'] as $key => $value) {
//                    $add .= $key . " LIKE ?";
//                    $newParams[] = $value;
//                }
//            }
            if (array_key_exists('where', $params)){
                    $i = 1;
                    foreach ($params['where'] as $key => $value) {
                        if ($i == count($params['where'])){
                            $add .= $key . " = ? ";
                            $newParams[] = $params['where'][$key];
                            break;
                        }
                        $add .= $key . " = ? AND ";
                        $newParams[] = $params['where'][$key];
                        $i++;
                    }
            }
            if (array_key_exists('like', $params)){
                foreach ($params['like'] as $key => $value) {
                    $add .= $key . " LIKE ?";
                    $newParams[] = "%" . $value . "%";
                }

            }
            /*
             * For limit to work, you must turn off the emulation mode. This can be done in db.php, specify PDO :: ATTR_EMULATE_PREPARES => false in the ['options'] key.
             */
            if (array_key_exists('limit', $params)){
                if (count($params['limit']) > 1){
                    $values = '';
                    $i = 1;
                    foreach ($params['limit'] as $value) {
                        if ($i == count($params['limit'])){
                            $values .= '?';
                            $newParams[] = $value;
                            break;
                        }
                        $values .= '?,';
                        $newParams[] = $value;
                        $i++;
                    }
                    $add .= " LIMIT {$values}";
                }elseif(count($params['limit']) == 1){
                    $add .= " LIMIT ?";
                    $newParams[] = $params['limit'][0];

                }
            }
        $sql = $sql . $add;
        return $this->conn->execute($sql, $newParams);
    }


    public function queryBySql($sql, $params = [])
    {
        $this->sql = $sql;
        $this->params = $params;
        return $this;
    }


    public function run()
    {
        return $this->conn->query($this->sql, $this->params);
    }


    public function findAll()
    {
        $res = $this->conn->run($this->sql, $this->params);

        if ($res){
            return $res->fetchAll($this->fetchMode);
        }

    }


    public function findOne()
    {
        $res = $this->conn->run($this->sql, $this->params);

        if ($res){
            return $res->fetch($this->fetchMode);
        }
    }
}