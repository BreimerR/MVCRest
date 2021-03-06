<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/26/20
 * Time: 7:23 PM
 */


/*if model has no rows throw error*/

/**TODO CREATE TABLE `entities` ()
 * @property bool $hasConnection
 */
abstract class Model
{

    protected static $INSTANCE;

    private static $instances = [];

    public $conn = null;

    private static $tableNames = [];

    private array $columns;

    protected function __construct($conn = null)
    {
        $this->conn = static::getConnection();
    }

    abstract function getConnection();

    /**
     * @return static
     */
    static function getInstance()
    {

        $class = static::class;

        return isset(self::$instances[$class]) ?
            self::$instances[$class] :
            self::$instances[$class] = new $class();

    }

    static function getDataClass(): string
    {
        $fullName = static::class;

        $len = strlen($fullName);

        $plural = substr($fullName, 0, $len - strlen("Model"));

        if (!isset(self::$tableNames)) {
            self::$tableNames[$fullName] = strtolower($plural);
        }

        return str_check_end_replace_with($plural, ["ies", "s"], ["y", ""]);
    }

    static function getCreateSql()
    {
        $sql = "CREATE TABLE ";

        /**@var Table $table */
        $table = static::getDataClass();

        $tableName = static::getTableName();

        $sql .= "`{$tableName}` (";

        $columns = $table::getTableColumns();

        $cols = array_map(function (Column $col) {
            return $col->getSql();
        }, $columns);

        $constraints = $table::getConstraints();

        $sql .= join(",", $cols);
        /*sample
           constraint table_name_entity_id_fk foreign key (entity) references entity (id),
        */
        $s = ",\n\t";
        $sql .= (count($constraints) > 0 ? $s : "") . join($s, $constraints);

        $sql .= "\n)";

        return $sql;
    }


    static function getTableName($class = null): string
    {
        $class = $class ?: static::class;

        $tableNames = self::$tableNames;

        if (isset($tableNames[$class])) {
            return $tableNames[$class];
        }

        return $tableNames[$class] = str_check_end_replace_with(strtolower($class), ["model"], [""]);

    }

    public function __get($name)
    {
        switch ($name) {
            case "hasConnection":
                return $this->conn->hasConnection;
                break;
        }

        return null;
    }
}





