<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/27/20
 * Time: 9:33 PM
 */

require_once "functions.php";
require_once "Type.php";

abstract class CompoundType
{

    /**
     * a compound type has fields/ sub types
     */

    protected $data;

    private static array $tableColumns = [];
    private static array $tableForeigns = [];
    private static ?array $tableConstraints = [];

    public function __construct($data = [])
    {
        $this->setDataExternal($data);
    }

    /**
     * @param array $data
     */
    private function setDataInternal(array $data): void
    {
        $this->data = [];

        foreach ($data as $columnName => $value) {
            $this->data[$columnName] = $value;
        }
    }

    /**
     * @param array $data
     */
    public function setDataExternal(array $data): void
    {
        $this->validateSetColumns($data);

        $this->setDataInternal($data);
    }

    /**
     * @param array $data
     */
    public function validateSetColumns(array $data)
    {

        /**@var CompoundType $self */
        $self = static::class;

        $fields = $self::getTableColumns();

        foreach ($data as $colName => $value) {
            if (!in_array($colName, $fields)) {
                $tableName = $self::getTableName();
                throw new InvalidArgumentException("column($colName) is not part of the database table $tableName");
            }
        }

    }

    static function getTableName(): string
    {
        $class = strtolower(static::class);

        $len = strlen($class);

        if ($class[$len - 1] == "y") $class = substr($class, 0, $len - 1) . "ie";

        $class .= "s";

        return $class;
    }

    /**
     * @param $columnName
     * @param string $parentModel
     * @param array $changeValues
     * @return Type|null
     * @throws MissingPrimaryKeyException
     */
    /**@private should be internal
     * @param $columnName
     * @param string $parentTable
     * @param string|null $childColName
     * @param array $changeValues
     * @return Type
     * @throws MissingPrimaryKeyException
     */
    public static function registerForeignKey($columnName, string $parentTable, string $childColName = null, ...$changeValues): Type
    {
        /**@var CompoundType $parentTable */

        /**@var Type[] $cols */
        $cols = $parentTable::getTableColumns();

        foreach ($cols as $column) {
            if ($column->isPrimary() || $column->name == $childColName) {

                /**@var Model */
                $class = static::class;

                isset(self::$tableForeigns[$class]) ?: self::$tableForeigns[$class] = [];

                self::$tableForeigns[$class][$columnName] = [$parentTable, $column->name];

                return $column->copy(...[$columnName, ...$changeValues]);
            }

        }

        throw new MissingPrimaryKeyException(self::getTableName());
    }

    static function mapParams(...$args): array
    {
        $params = [];
        $keys = array_keys(self::getTableColumns());

        $required = count($keys);
        $count = count($args);

        if ($count <= $required) {
            // TODO some columns might be nullable and this might not play nice with them
            throw new InvalidArgumentException("Required $required column values found $count");
        } else if ($count >= $required) {
            throw new InvalidArgumentException("Required $required columns values found $count");
        }

        foreach ($args as $i => $value) {
            $params[$keys[$i] = $value];
        }

        return $params;
    }

    abstract static function initColumns(): array;

    /**/
    public static function getTableColumns(): array
    {
        $class = Model::getTableName(static::class);

        return isset(self::$tableColumns[$class]) ? self::$tableColumns[$class] : self::$tableColumns[$class] = static::initColumns();
    }

    static function initConstraints(): array
    {

        $tableName = Model::getTableName(static::class);

        $constraints = [];

        if (isset(self::$tableForeigns[static::class])) {
            $foreigns = self::$tableForeigns[static::class];

            foreach ($foreigns as $columnName => $foreign) {

                [$foreignTable, $foreignCol] = $foreign;

                $foreignTable = $foreignTable::getTableName();

                $tableName = static::getTableName();

                array_push(
                    $constraints,
                    "constraint {$tableName}_{$foreignTable}_{$foreignCol}_fk foreign key ($columnName) references $foreignTable ($foreignCol)"
                );

            }

        }

        return $constraints;
    }


    static function getConstraints($class = null): array
    {
        $class = $class ?: static::class;
        return isset(self::$tableConstraints[$class]) ? self::$tableConstraints[$class] : self::$tableConstraints[$class] = self::initConstraints();
    }

    /**
     * This should check what format the user requested to get the  data
     * in if in json th project should process the fields as
     * TODO this should not be here as the format is gotten from the controller of the API
     */
    public function __toString()
    {

        return json_encode($this->data);
    }

    /**
     * @param $columnName
     * @param string $relativeTable
     * @param array $changeValues
     * @return Type|null
     * @throws MissingPrimaryKeyException
     */
    // should return the primary key of this table it's type etc
    public static function dependency($columnName, string $relativeTable, ...$changeValues): ?Type
    {
        /**@var CompoundType $relativeTable */

        /**@var Type[] $cols */
        $cols = self::getTableColumns();

        foreach ($cols as $column) {
            if ($column->isPrimary()) {

                $tName = self::getTableName();

                $relativeTable::registerForeignKey([$columnName => [$tName, $column->name]]);

                return $column->copy(...[$columnName, ...$changeValues]);
            }
        }

        throw new MissingPrimaryKeyException(self::getTableName());
    }

    /**@throws MissingColumnException */
    public static function getCol($name): Type
    {
        /**@var Type[] */
        $columns = self::getTableColumns();
        foreach ($columns as $column) {
            if ($column->name == $name) {
                return $column;
            }
        }

        throw new MissingColumnException(self::getTableName(), $name);
    }

}

class MissingColumnException extends Exception
{
    public function __construct(string $tableName, $columnName, $code = 0, Throwable $previous = null)
    {

        $message = "Missing column($columnName) from table $tableName";
        parent::__construct($message, $code, $previous);
    }
}

class MissingPrimaryKeyException extends Exception
{
    public function __construct(string $tableName, $code = 0, Throwable $previous = null)
    {
        $message = "No valid primary key for table($tableName).";

        parent::__construct($message, $code, $previous);
    }
}
