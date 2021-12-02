<?php
namespace app\lib;
use app\lib\Singleton;
use PDO;
use PDOException;
use app\lib\database\Connect;


class Db extends Singleton
{
    public static $db;
    public static $conn;
    public static $query = '';
    public static $params = [];
    public static $mixParams = [];
    public static $keyParams = [];
    public static $methodArrs = [];
    public static $stmt;
    public static $table;
    public static $select = '';
    public static $insert;
    public static $update;
    public static $delete;
    public static $where = '';
    public static $like = '';
    public static $limit = '';
    public static $by_order = '';
    public static $in = '';
    public static $group_by = '';
    public static $pdo_fetch = ['PDO::FETCH_COLUMN' => PDO::FETCH_COLUMN, 'PDO::FETCH_BOTH' => PDO::FETCH_BOTH, 'PDO::FETCH_NUM' => PDO::FETCH_NUM,
        'PDO::FETCH_ASSOC' => PDO::FETCH_ASSOC, 'PDO::FETCH_OBJ' => PDO::FETCH_OBJ, 'PDO::FETCH_LAZY' => PDO::FETCH_LAZY, 'PDO::FETCH_KEY_PAIR' => PDO::FETCH_KEY_PAIR,
        'PDO::FETCH_UNIQUE' => PDO::FETCH_UNIQUE, 'PDO::FETCH_GROUP' => PDO::FETCH_GROUP, 'PDO::FETCH_CLASS' => PDO::FETCH_CLASS, 'PDO::FETCH_CLASSTYPE' => PDO::FETCH_CLASSTYPE, 'PDO::FETCH_PROPS_LATE' => PDO::FETCH_PROPS_LATE,
        'PDO::FETCH_INTO' => PDO::FETCH_INTO, 'PDO::FETCH_SERIALIZE' => PDO::FETCH_SERIALIZE, 'PDO::FETCH_FUNC' => PDO::FETCH_FUNC, 'PDO::FETCH_NAMED' => PDO::FETCH_NAMED, 'PDO::FETCH_BOUND' => PDO::FETCH_BOUND];




    protected static function getself()
    {
        if (static::$db === null)
        {
            static::$db = static::getInstance();;
        }

        return static::$db;
    }





    public static function conn()
    {
        try {
            self::$conn = Connect::connect();
            var_dump(self::$conn);die;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

        self::$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        return self::getself();
    }





    public static function table(string $table)
    {
        self::conn();
        self::$table = $table;
        return self::getself();
    }





    public static function select(array $arr)
    {
        $columns = '';
        for ($i = 0; $i < count($arr); $i++) {
            if ($i == count($arr) - 1){
                $columns .= $arr[$i] . ' ';
                break;
            }
            $columns .= $arr[$i] . ',';
        }

        self::$select = 'SELECT ' . $columns . 'FROM ' . self::$table;
        return self::getself();
    }





    public static function where(array $arr)
    {
        $i = 0;
        foreach ($arr as $key => $val) {
            $column = $key;
            $keyy = 'where' . "$i";
            self::$mixParams['where'][$keyy] = $val;
            $i++;
        }
        self::$where = ' WHERE ' . $column . '=' . '?';
        return self::getself();
    }





    public static function run()
    {
        self::$methodArrs = ['insert' => self::$insert, 'update' => self::$update, 'delete' => self::$delete];

        foreach (self::$methodArrs as $key => $method) {
            if ($method != null) {
                foreach (self::$mixParams[$key] as $item) {
                    self::$params[] = $item;
                }
                self::$stmt = self::$conn->prepare(self::$query);
                self::$stmt->execute(self::$params);
            }
        }
    }





    public static function insert(array $arr)
    {
        $columns = '';
        $values = '';
        $i = 0;
        foreach ($arr as $key => $value) {
            $keyy = 'insert' . "$i";
            if ($i == count($arr) - 1) {
                $columns .= $key;
                $values .= '?';
                self::$mixParams['insert'][$keyy] = $value;
                break;
            }
            $i++;
            $columns .= $key . ',';
            $values .= '?,';
            self::$mixParams['insert'][$keyy] = $value;
        }


        self::$insert = 'INSERT INTO ' . self::$table . ' (' . $columns . ')' . ' VALUES ' . '(' .$values . ')';
        self::$query .= self::$insert;
        self::run();
        return self::getself();
    }





    public static function update(array $arr,)
    {
        $set = '';
        $where = '';
        $i = 0;
        foreach ($arr[0] as $key => $value) {
            $keyy = 'update' . "$i";
            if ($i == count($arr[0]) - 1){
                $set .= $key . ' = ?';
                self::$mixParams['update'][$keyy] = $value;
                break;
            }
            $set .= $key . ' = ?, ';
            self::$mixParams['update'][$keyy] = $value;
            $i++;

        }
        $k = 0;
        $one = 1;
        foreach ($arr[1] as $key => $value) {
            $keyy = 'update' . $i + $k + $one;
            if ($k == count($arr[1]) - 1){
                $where .= $key . ' = ?';
                self::$mixParams['update'][$keyy] = $value;;
                break;
            }
            $where .= $key . ' = ?,';
            self::$mixParams['update'][$keyy] = $value;
            $k++;
        }

        self::$update = 'UPDATE ' . self::$table . ' SET ' . $set . ' WHERE ' .$where;
        self::$query .= self::$update;
        self::run();
        return self::getself();
    }





    public static function delete(array $arr)
    {
        $i = 0;
        foreach ($arr as $key => $val) {
            $keyy =  'delete' . "$i";
            $column = $key;
            $value = $val;
            self::$mixParams['delete'][$keyy] = $value;
            $i++;
        }

        self::$delete = 'DELETE FROM ' . self::$table .' WHERE ' . $column . '=' . '?';
        self::$query .= self::$delete;
        self::run();
        return self::getself();
    }





    public static function like(array $arr)
    {
        $i = 0;
        foreach ($arr as $key => $val) {
            $keyy =  'like' . "$i";
            $column = $key;
            self::$mixParams['like'][$keyy] = "%$val%";
            $i++;
        }

        self::$like = ' WHERE ' . $column . ' LIKE ' . '?';
        return self::getself();
    }





    public static function limit(array $arr)
    {
        self::$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $start = $arr[0];
        $finish = $arr[1];
        $keyy = 'limit' . '0';
        self::$mixParams['limit'][$keyy] = $start;
        $keyy = 'limit' . '1';
        self::$mixParams['limit'][$keyy] = $finish;
        self::$limit = ' LIMIT ' . '?,' . '?';
        return self::getself();
    }





    public static function in(array $arr)
    {
        $i = 0;
        foreach ($arr as $key => $value) {

            $in  = str_repeat('?,', count($value) - 1) . '?';
            $column = $key;
            foreach ($value as $item) {
                $keyy = 'in' . "$i";
                self::$mixParams['in'][$keyy] = $item;
                $i++;
            }


        }
        self::$in .= ' WHERE ' . $column . ' IN ' . '(' . $in . ')';
        return self::getself();
    }




    public static function creatorSQL()
    {
        self::$query .= self::$select . self::$insert . self::$update . self::$delete . self::$where . self::$like . self::$limit . self::$in;
        self::$methodArrs = ['where' => self::$where, 'like' => self::$like, 'limit' => self::$limit, 'in' => self::$in];

        foreach (self::$methodArrs as $key => $method) {
            if ($method != null){
                foreach (self::$mixParams[$key] as $item) {
                    self::$params[] = $item;
                }
            }
        }

        self::prepare();
    }

    public static function prepare()
    {
        self::$stmt = self::$conn->prepare(self::$query);
        self::$stmt->execute(self::$params);
    }



    public static function fetchAll($fetch = PDO::FETCH_ASSOC)
    {
        self::creatorSQL();
        return self::$stmt->fetchAll($fetch);
    }





    public static function fetch($fetch = PDO::FETCH_ASSOC)
    {
        self::creatorSQL();
        return self::$stmt->fetch($fetch);
    }
}