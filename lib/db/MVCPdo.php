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
require_once "DbTemplate.php";
require_once "Json.php";


/**
 * Contains actions relating to the specified
 * database and creating tables is not part of it's
 * job
 * TODO sort out depending on arguments count?
 * @method select($tableName, $items, $where, $extras = [])
 * @method update(string $tableName, array $set, array $where)
 * @method insert($tableName, $items)
 * @method create($tableName, $columns)
 * @method delete($tableName, $where)
 * @method drop($tableName)
 * @method dropTable($tableName)
 * @method truncate($tableName)
 * // $action ="DROP"
 * @method alter($tableName, $action)
 * @property array|false|mixed|string|null error
 * @property array|string|string[]|null sql
 * @property array|string|string[]|null preparedSql
 * @property bool hasError
 */
abstract class MVCPdo extends MVCDatabase
{

    public static int $ERROR_LEVEL = 1;

    public static int $LIGHT_ERRORS = 1;

    protected ?array $_extras = null;

    use DbTemplate;

    function cleanUp()
    {
        $this->_bindValues = [];
        $this->_bindKeys = [];
        $this->_errors = [];
        $this->_extras = null;
        $this->_sql = null;
        $this->_preparedSql = null;
        $this->_preparedStatement = null;

    }

    /**
     * @param Exception $e
     * @param null $message
     * @return bool
     * @throws Exception
     */
    function handleException(Exception $e, $message = null)
    {
        switch (self::$ERROR_LEVEL) {
            case self::$LIGHT_ERRORS:
                $this->error = $message ?: $e->getMessage();
                return false;
                break;
            default:
                throw $e;

        }
    }

    /**
     * @param $preparedStatement
     * @param $keys
     * @param $values
     * @param null|bool $countsValidation
     * @return bool
     * @throws Exception
     */
    public function bind($preparedStatement, $keys, $values, $countsValidation = null)
    {
        $countsValidation = $countsValidation == null ? count($keys) == count($values) : $countsValidation;

        try {
            if ($countsValidation) {
                /** @noinspection PhpStatementHasEmptyBodyInspection */
                for ($i = 0; $i < count($values); $preparedStatement->bindParam($keys[$i], $values[$i]), $i++) ;
            } else {
                // should throw an error
            }
        } catch (PDOException $e) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->handleException($e);
        }

        $this->_preparedStatement = $preparedStatement;

        return $this->hasError;
    }

    public function prepare($sql)
    {
        $this->_preparedStatement = $this->conn->prepare($sql);
    }

    function query($sql = null, $binds = [])
    {
        if ($this->conn == null) {
            $this->error = "Database not instantiated.";
            return false;
        }

        !empty($this->_sql) ?: ($this->_sql = $sql)($this->_sql = $this->prepare($this->_sql));

        return $this->execute();
    }

    protected function prepareSql()
    {
        $this->prepare($this->_preparedSql);
    }

    public function bindPreparedStatement()
    {
        $this->prepareSql();
        $this->bind($this->_preparedStatement, $this->_bindKeys, $this->_bindValues, true);
    }

    public function _update($table, $set, $where)
    {
        $i = 0;
        $cols = [];

        foreach ($set as $name => $value) {
            $key = ":key_$i";
            array_push($this->_bindValues, $value);
            array_push($this->_bindKeys, $key);
            array_push($cols, "$name = $key");

            $i += 1;
        }

        $this->_preparedSql .= " `$table` SET " . implode(",", $cols);

        $this->prepWheres($where);

        $this->_preparedSql .= "--";
    }

    function prepWheres($wheres)
    {
        [$wheres] = $this->breaker($wheres);

        $this->_preparedSql .= " WHERE " . implode(" ", $wheres);
    }

    /**
     * DELETE FROM tableName WHERE wheres
     * @param $table
     * @param true|array $wheres
     */
    public function _delete($table, $where)
    {
        $this->_preparedSql .= "FROM `$table`";

        $this->prepWheres($where);

        $this->_preparedSql .= "--";
    }

    /**
     * TODO % is  bound wrong it should not be part of the value when binding
     * SELECT * FROM `users` WHERE name LIKE value%
     * @param $table
     * @param $items
     * @param $where
     * @param array $extras
     */
    public function _select($table, $items, $where, $extras = [])
    {
        $items = is_string($items) ? explode(",", $items) : $items;

        $columns = implode('`,`', $items);

        $columns = trim($columns);

        $this->_preparedSql .= ($columns == "*" ? "*" : "`$columns`") . " FROM `$table`";

        $this->prepWheres($where);

        $this->_preparedSql .= " " . implode(" ", $extras);

        $this->_preparedSql .= "--";
    }

    public function prepareInsertValues($items)
    {

        return array_map(function ($values) {
            $i = count($this->_bindValues);

            $keys = [];

            foreach ($values as $value) {
                $key = ":key_$i";
                array_push($this->_bindKeys, $key);
                // null is not accounted for.
                array_push($this->_bindValues, $value);
                array_push($keys, $key);
                $i += 1;
            }

            return "(" . implode(",", $keys) . ")";
        }, array_values($items));

    }

    /**
     * INSERT INTO tableName SET (column[,..]) VALUES values,values,values
     * $keyValues = [
     *      "columnName" => values["value", "value"],
     *      "column2Name" => values["value2", "value2"]
     * ];
     */
    public function _insert($table, $items)
    {
        $columns = is_array($items) ? array_keys($items) : $items;

        $values = $this->prepareInsertValues($items);

        $columns = implode('`,`', $columns);

        $columns = trim($columns);

        $this->_preparedSql .= "INTO `$table` (`$columns`) VALUES \n" . implode(",\n", $values) . "--";

    }

    function createDrop($table)
    {
        $this->_preparedSql .= "TABLE `$table`";
    }

    public function _create($table, $columns, $constraints = [], $indices = [])
    {
        $this->createDrop($table);

        $constraints = count($constraints) > 0 ? "," . implode(",", $constraints) : "";
        $this->_preparedSql .= "(" .
            implode(",", $columns) .
            $constraints .
            ");";

    }

    public function _drop($table)
    {
        $this->createDrop($table);

        $this->_preparedSql .= "--";
    }

    public function _dropTable($table, $ifExists = false)
    {
        $this->_preparedSql = "DROP TABLE ";

        $this->_preparedSql .= ($ifExists ? "IF EXISTS " : "") . "`$table`" . "--";
    }

    function lastUpdatedId($table)
    {
        $db = self::getInstance();

        $db->select($table, ["id/>=/1"], "id", "ORDER BY `id` DESC");

        return $db->fetchAllObject()[0]->id;
    }

    /**
     * @param object|null $class
     * @return array
     */
    function fetchAllObject($class = null)
    {

        $this->_assoc = $this->fetchAll();

        $ObjArray = array();

        foreach ($this->_assoc as $item) {
            //  array_push($ObjArray, new ArrayO($item));
        }

        return $ObjArray;
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

    function commit()
    {
        $this->conn->commit();
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

    /**
     * This is meant to handle columnName/operator/columnValue
     * @param string|array $items
     * @param int $k
     * @return array
     */
    private function breaker($items = [], $k = 0)
    {
        $params = $wheres = $keys = [];

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

                // rejoin if the / was part of the value
                for ($i = 3; $i < $count; $i++) {
                    $val .= "/" . $item[$i];
                }

                if (isset($item[3])) {
                    $vals = [];
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
                    $likes = ["%", "^"];
                    //TODO repair for ^value%
                    array_push($this->_bindKeys, $key = ":key_$k");
                    array_push($wheres, "{$col} {$op} $key");
                    // This is because we are using strings thus null will be '' we need to make NULL valid as an input value
                    array_push($params, $val == '' ? NULL : ($val == 'true' ? TRUE : ($val == 'false' ? FALSE : $val)));
                    array_push($this->_bindValues, $val);
                    // array_push($cols, "{$col}");

                    $k++;
                }


            } else  $this->error = "Malformed where =" . (is_string($item) ? $item : (is_array($item) ? Json::parse($item) : ''));
        }

        return array($wheres, $params);
    }

    /**
     * $items = "item" | "*" | "item,item1,item2,..." | ["item","item1","item2"]
     * @param string|array $items
     * @return array
     */
    function formatItems($items = ""): array
    {

        if (!is_array($items)) {
            if (is_string($items) && trim($items) == "") return [];

            $items = explode(',', $items);
        }
        return $items;
    }


    function execute()
    {
        try {
            $this->_preparedStatement->execute();
        } catch (PDOException $e) {
            $this->error = "sql = " . $this->sql . "\n" . $e->getMessage();
        }
    }

    public function __call($action, $arguments)
    {
        $this->cleanUp();

        if (method_exists($this, "_$action")) {

            $this->_preparedSql = strtoupper($action) . ' ';

            call_user_func_array([$this, "_$action"], $arguments);

            $this->bindPreparedStatement();

            $this->execute();

            $this->query($this->_preparedSql, $this->_bindKeys, $this->_bindValues);

        }

    }

}