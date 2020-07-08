<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/28/20
 * Time: 1:56 PM
 */

require_once "MVCDatabase.php";
require_once "SQLStubs.php";


/**
 * @method select()
 * @property array|false|mixed|string|null error
 * @property array|string|string[]|null sql
 * @property bool hasError
 */
abstract class MVCPdo extends MVCDatabase
{


    use SQLStubs;

    /**
     * @param PDO $conn
     */
    protected object $conn;
    // current sql table
    protected string $table;
    // current created sql
    /**
     * sql changes before any new execution
     */
    protected ?string $_sql = null;

    protected ?string $_sqlStatement = null;

    /**@var PDOStatement|null $preparedStatement */
    protected ?PDOStatement $preparedStatement = null;

    private array $_bindValues = [];

    private array $_constraints = [];

    private array $_errors = [];

    use OnCreateCallBack;

    use OnDestroyCallBack;

    protected $preparedSql = null;
    /**
     * the value of the row that has been affected
     * most recently
     */
    protected $lastUpdatedId = null;


    abstract function connect($configs): PDO;

    protected function __construct($settings = [])
    {
        try {
            $this->conn = $this->connect(static::getConfigs());
        } catch (PDOException $e) {
            /*TODO handle this better*/
            switch ($code = $e->getCode()) {
                case  1049:
                    // db does not exist
                    createDatabase();
                    break;
                case  2002:
                    // db connection refused
                    echo "Failed to get access to  provided host ";
                    break;
                default:
                    echo $e->getMessage();
            }
        }
    }

    /**
     * @param $table
     * @return mixed
     */
    function lastUpdatedId($table)
    {
        $db = self::getInstance();

        $db->select($table, ["id/>=/1"], "id", "ORDER BY `id` DESC");

        return $db->fetchAllObject()[0]->id;
    }

    /**
     * @param null $merg
     * @return array
     */
    function fetchAllObject($merg = null)
    {

        $this->_assoc = $this->fetchAll();

        $ObjArray = array();

        foreach ($this->_assoc as $item) {
            array_push($ObjArray, new ArrayO($item));
        }

        return $ObjArray;
    }

    /**
     * @return mixed
     */
    function fetchAll()
    {
        try {
            return $this->_assoc = $this->preparedStatement->fetchAll();
        } catch (PDOException $e) {
            $this->addError($e);
            return [];
        }
    }

    protected function addError($error)
    {
        array_push($this->_errors, $error);
    }

    /**
     * @param $table
     * @param array|string $where string = "col/=/value"
     * @param $items
     * @param string $extras what to put after the sql statement.
     * @return bool
     */
    function _select($table, $where = [], $items, $extras = '')
    {
        $this->select($table, $where, $items, $extras);

        return !$this->errorExists();
    }

    /**
     * @return bool
     */
    function errorExists()
    {
        return !StringO::isEmpty($this->_err) || !StringO::isEmpty($this->_error);
    }

    function fetch()
    {

    }

    /**
     * @return mixed
     */
    function fetchAssoc()
    {
        return $this->fetchAll();
    }

    /**
     * @return mixed
     */
    function fetchObject()
    {
        return $this->_res->fetchObject();
    }

    /**
     * @param null $structure
     * @return mixed
     */
    function first($structure = null)
    {
        $res = $this->_res->fetchAll();

        return $res[0];
    }

    /**
     * @param $success
     * @param $fail
     * @param null $data
     * @return mixed
     */
    function onExec($success, $fail, $data = null)
    {
        return ($this->_exec) ? $success($data) : $fail($this->_error);
    }

    /**
     * @param $dbname
     */
    function deletedb($dbname)
    {
        // TODO: Implement deletedb() method.
    }

    /**
     * @param $table
     */
    function deleteTable($table)
    {
        $this->prepare("DELETE TABLE :key_0");

        $this->bindPreparedStatement([':key_0'], [$table]);

    }

    /**
     * @param PDOStatement $preparedStatement
     * @param $keys
     * @param $values
     * @return bool
     */
    public function bind($preparedStatement, $keys, $values)
    {
        try {
            /** @noinspection PhpStatementHasEmptyBodyInspection */

            for ($i = 0; $i < count($values); $preparedStatement->bindParam($keys[$i], $values[$i]), $i++) ;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }

        $this->preparedStatement = $preparedStatement;

        return $this->hasError;
    }

    protected function bindPreparedStatement($keys, $values)
    {
        return $this->bind($this->preparedStatement, $keys, $values);
    }


    function getLastUpdatedId(): int
    {
        return $this->lastUpdatedId;
    }

    /**
     * @return bool
     */
    function execute()
    {
        try {
            return $this->preparedStatement->execute();
        } catch (PDOException $e) {
            $this->error = $e->getMessage() . ' ' . $this->sql;
        }

        return false;
    }

    function commit()
    {
        $this->conn->commit();
    }

    /**
     * @param $table
     * @return bool
     */
    function tableExists($table)
    {

        $this->query("SHOW TABLES LIKE '" . $table . "'");

        return (bool)$this->count();
    }

    /**
     * @return mixed
     */
    function count()
    {
        return $this->preparedStatement->rowCount();
    }

    /**
     * @param $sql
     * @param array $values
     * @return PDOStatement
     */
    public function customQuery($sql, $values = [])
    {
        $this->_error = $this->_err = '';

        $this->_sql = $sql;

        if ($c = count($values)) {
            $keys = [];

            /** @noinspection PhpStatementHasEmptyBodyInspection */
            for ($i = 0; $i < $c; array_push($keys, ":key_$i"), $i++) ;

            $stmnt = $this->conn->prepare($this->_sql);

            /** @noinspection PhpStatementHasEmptyBodyInspection */
            for ($i = 0; $i < $c; $stmnt->bindParam($keys[$i], $values[$i]), $i++) ;

            return $stmnt;
        }


        return $this->_sql = $this->conn->prepare($this->_sql);
    }

    /**
     * returns bool if error handling is in soft mode
     * and not exception mode
     * @param $preparedStatement
     * @return PDOStatement|bool
     */
    function prepare($preparedStatement)
    {
        $this->preparedStatement = $preparedStatement;
        return $this->conn->prepare($preparedStatement);
    }


    /**
     * @param string|array $items
     * @return array
     */
    function formatItems($items = ""): array
    {
        if ($items == "") return [];
        if (!is_array($items)) $items = explode(',', $items);
        return $items;
    }

    /**
     * stractureSql("select","tableName")
     * @param $action
     * @param null $table
     * @param null $items
     * @return string
     */
    public function prepareSqlStart($action, $table = null, $items = [])
    {
        if ($items == null) throw new Error("Last argument(\$items) can not be null");

        $items = implode('`,`', $items);

        $this->_sql = strtoupper($action) . ' ';

        $items = trim($items);

        switch (strtolower($action)) {

            case "insert":
                return $this->_sql .= " INTO `$table`";

            case "update":
                return $this->_sql .= " `$table` SET ";

            case "select" || "delete":
                $this->_sql .= $items == "*" ? "*" : "`$items`";

        }

        return $this->_sql .= " FROM `$table` WHERE ";
    }

    /**
     * @param $act
     * @param array $where
     * @param null $w
     * @return bool
     */
    private function completeSql($act, $where = [], $w = null)
    {
        /**
         *  UPDATE table_name SET/ `col` = 'value' ,`col` = 'val' WHERE  `col_name` = 'val'--
         * "SELECT `username`, `password` FROM $table  WHERE/ `username`=`?`--";
         * "DELETE  FROM $table WHERE / `username` =`?` --";
         */
        $a = $this->breaker($where);

        if (count($a) > 2) {
            [$wheres, $vals, $keys] = $a;

            switch ($act) {
                case 'insert':
                    /**
                     * "INSERT INTO `tableName`(`id`, `item_name`)  /  VALUES (`?`,`?`,`?`)--";
                     */
                    $this->_sql .= "(`" . implode($a[3], '`,`') . "`) VALUES(" . implode(',', $keys) . ")";

                    $this->prepare($this->_sql);

                    return $this->bindPreparedStatement($keys, $vals);
                    break;
                case "select" || "delete":
                    /**
                     * "SELECT/DELETE [items];
                     */
                    $this->_sql .= implode(' ', $wheres) . ' ';
                    break;
                default:
                    $this->_sql .= implode(', ', $wheres) . ' ';
                    break;
            }

            if ($act == 'update') {
                $w = $this->breaker($w, count($keys));
                $this->_sql .= ' WHERE ' . implode(' ', $w[0]);

                $vals = array_merge($vals, $w[1]);
                $keys = array_merge($keys, $w[2]);
            }

            if ($this->_constraints) {
                $this->_sql .= (is_array($this->_constraints)) ? implode(" ", $this->_constraints) : $this->_constraints;
            }
            $this->prepare($this->_sql);
            return $this->bindPreparedStatement($keys, $vals);
        } else $this->error = json_encode($a);

        return false;
    }

    private function completeUpdateSql($act, $where = [], $w = [])
    {

    }

    function query($sql = null)
    {
        if ($this->conn == null) {
            $this->error = "Database not instantiated.";
            return false;
        }

        !empty($this->_sql) ?: ($this->_sql = $sql)($this->_sql = $this->conn->prepare($this->_sql));

        return $this->execute();
    }

    /**
     * This is meant to handle columnName/operator/columnValue
     * @param array $items
     * @param int $k
     * @return array
     */
    private function breaker($items = [], $k = 0)
    {
        $params = $where = $ky = $cols = [];

        $o = ["!=", '=', ">=", "<=", '*', "<>", '<', '>', "BETWEEN", "LIKE", "TRUE", "IS", "IS NOT"];

        // make array into an array if it is not an array i.e if a string is provided
        is_array($items) ?: $items = [$items];


        foreach ($items as $item) {
            /*http://www.facebook.com*/
            // the breaker has a problem
            // need to migrate to better options
            // the forward slash is used in so many places
            if (($count = count($item = explode('/', $item))) >= 3) {
                $col = $item[0];
                $val = $item[2];

                //TODO VERIFY WORKING
                // rejoin if the / was part of the value
                for ($i = 3; $i < $count; $i++) {
                    $val .= "/" . $item[$i];
                }


                /*TODO check on this quick fix for better solutions*/
                // @Solved Had a small problem with the explode thus we had to remove the / item
                // then at the end we would return the removed required /
                if (isset($item[3])) {
                    $vals = array();
                    for ($i = 2; $i < count($item); $i++) {
                        array_push($vals, $item[$i]);
                    }
                    $val = implode('/', $vals);
                }

                $b = $op = !1;
                for ($i = 0, $c = (count($o) - 1); $i < count($o); $i++, $c--) {
                    if ($b = ($o[$i] == ($op = $item[1]) || $o[$c] == $op)) {
                        break;
                    }
                }


                if ($b) {
                    array_push($ky, $key = ":key_$k");
                    array_push($where, "{$col} {$op} $key");
                    // This is because we are using strings thus null will be '' we need to make NULL valid as an input value
                    array_push($params, $val == '' ? NULL : ($val == 'true' ? TRUE : ($val == 'false' ? FALSE : $val)));
                    array_push($this->_bindValues, $val);
                    array_push($cols, "{$col}");
                    $k++;
                }


            } else  $this->error = "Malformed where =" . (is_string($item) ? $item : (is_array($item) ? Json::parse($item) : ''));
        }
        return array($where, $params, $ky, $cols);
    }

    /**
     * @param $f
     * @param $arguments
     * @return bool
     */
    function __call($f, $arguments)
    {

        $f = strtolower($f);
        // reset values for each query call
        $this->_bindValues = [];
        $this->_constraints = [];

        // reset errors for each query call
        $this->_errors = [];

        // this should throw an exception here for no table name specified
        $this->prepareSqlStart($f,
            $table = isset($arguments[0]) ? $arguments[0] : "",
            $this->formatItems($items = isset($arguments[2]) ? $arguments[2] : [])
        );

        $this->_constraints = (isset($arguments[3]) ? $arguments[3] : []);

        $where = isset($arguments[1]) ? $arguments[1] : [];

        switch ($f) {
            case "update":
                $this->completeSql($f, $where, $items);
                return $this->query();
                break;
            case "select" || "delete" || "insert":
                $this->completeSql($f, $where);
                return $this->query();
                break;

            default:
                // TODO 'Check if is admin and allow to do more actions';
                return false;
                break;
        }
    }

    function __set($name, $value)
    {
        if ($name == "error") {
            $this->addError($value);
        }
    }

    public function __get($name)
    {
        switch ($name) {
            // returns the last available error
            case "error":
                $error = $this->_errors[count($this->_errors) - 1];
                // unset for other errors.

                return $error . "<br> SQL = " . $this->sql;

            case "errors":
                return $this->_errors;

            case "sql":
                $sql = $this->_sql;
                for ($i = 0; $i < count($this->_bindValues); $i++) {
                    $sql = str_replace(":key_$i", $this->_bindValues[$i], $sql);
                }
                return $sql;

            case "hasError":
                return count($this->_errors);

        }

        return null;
    }

}