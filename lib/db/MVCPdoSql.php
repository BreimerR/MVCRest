<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/28/20
 * Time: 6:53 PM
 */

require_once "MVCPdo.php";

abstract class MVCPdoSql extends MVCPdo
{

    /**
     * @param PDO $conn
     */
    protected $conn;

    function connect($configs): PDO
    {
        return new PDO(
            "mysql:host={$configs['host']};dbname={$configs['dbName']}",
            $configs['userName'],
            $configs['password']
        );
    }

    static function prepConfigs($host, $databaseName, $userName = "root", $password = "", $email = ""): array
    {
        return [
            "host" => $host,
            "databaseName" => $databaseName,
            "userName" => $userName,
            "password" => $password,
            "email" => $email
        ];
    }


    use OnCreateCallBack;

    use OnDestroyCallBack;


    /**
     * @return mixed
     */
    function count()
    {
        return $this->preparedStatement->rowCount();
    }





    /**
     * stractureSql("select","tableName")
     * @param $action
     * @param null $table
     * @param null $columns
     * @return string
     */
    public function structureSql($action, $table = null, $columns = [])
    {
        if ($columns == null) throw new Error("Last argument(\$items) can not be null");

        $columns = implode('`,`', $columns);

        $this->_sql = strtoupper($action) . ' ';

        $columns = trim($columns);

        switch (strtolower($action)) {

            case "insert":
                return $this->_sql .= " INTO `$table`";

            case "update":
                return $this->_sql .= " `$table` SET ";

            case "select" || "delete":
                $this->_sql .= $columns == "*" ? "*" : "`$columns`";

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

            if ($this->_extras) {
                $this->_sql .= (is_array($this->_extras)) ? implode(" ", $this->_extras) : $this->_extras;
            }
            $this->prepare($this->_sql);
            return $this->bindPreparedStatement($keys, $vals);
        } else $this->error = json_encode($a);

        return false;
    }

    private function completeUpdateSql($act, $where = [], $w = [])
    {

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
        $this->_extras = [];

        // reset errors for each query call
        $this->_errors = [];

        // this should throw an exception here for no table name specified
        $this->structureSql($f,
            $table = isset($arguments[0]) ? $arguments[0] : "",
            $this->formatItems($items = isset($arguments[2]) ? $arguments[2] : [])
        );

        $this->_extras = (isset($arguments[3]) ? $arguments[3] : []);

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



