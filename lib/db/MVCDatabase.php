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

    /**
     * get access to the database and query each model
     * @param string $host
     * @param $databaseName
     * @param string $userName
     * @param string $password
     * @param string $email
     * @return MVCDatabase
     */
    protected static array $configs = [
        "host" => "localhost",
        "databaseName" => "test",
        "userName" => "root",
        "password" => "",
        "email" => null
    ];

    /**@var String[] $models */
    public static array $models = [];

    static ?MVCDatabase $INSTANCE = null;

    abstract protected function __construct($settings = []);

    static function getInstance(): MVCDatabase
    {
        return self::$INSTANCE == null ? self::$INSTANCE = new static(static::getConfigs()) : self::$INSTANCE;
    }

    abstract static function prepConfigs($host, $databaseName, $userName = "root", $password = "", $email = ""): array;

    static function getConfigs(): array
    {
        return static::$configs;
    }

    static function generateModelsSql(): array
    {
        $SQLs = [];

        /**@var Model $model */
        foreach (static::$models as $model) {

            $sql = $model::getCreateSql();

            array_push($SQLs, $sql);
        }

        return $SQLs;
    }

    static function getConfig($config): string
    {
        if (isset(static::$configs[$config]))
            return static::$configs[$config];

        if (isset(self::$configs[$config]))
            return self::$configs[$config];
    }


}