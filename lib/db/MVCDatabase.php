<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/28/20
 * Time: 1:38 PM
 */

abstract class MVCDatabase
{

    /**TODO
     * 1. connect to the database
     * 2. if connects go to 5 else 3
     * 3. try to create the database if true 5 else 4
     * 4. if it fails to create throw no database connection available
     * 5. instanciate $conn
     */
    /**
     * @param object $conn
     */
    protected $conn;

    /**@var String[] $models */
    public static array $models = [];

    static MVCDatabase $INSTANCE;

    abstract function __construct($settings = []);

    /**
     * get access to the database and query each model
     * @param string $host
     * @param $databaseName
     * @param string $userName
     * @param string $password
     * @param string $email
     * @return MVCDatabase
     */
    static function getInstance($host, $databaseName, $userName = "root", $password = "", $email = ""): MVCDatabase
    {
        return self::$INSTANCE == null ? self::$INSTANCE = new static(static::prepConfigs($host, $databaseName, $userName, $password, $email)) : self::$INSTANCE;
    }

    abstract static function prepConfigs($host, $databaseName, $userName = "root", $password = "", $email = ""): array;

    static function generateModelsSql(): array
    {
        $SQLs = [];

        /**@var Model $model */
        foreach (static::$models as $model) {

            $sql = $model::generateSql();

            array_push($SQLs, $sql);
        }

        return $SQLs;
    }


}