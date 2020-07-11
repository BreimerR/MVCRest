<?php

/**
 * @property  Model[] $models
 */


class Chama extends MVCPdoSql
{

    protected static array $configs = [
        "host" => "localhost",
        "dbName" => "chama",
        "userName" => "root",
        "password" => ""
    ];

    /**TODO
     * The order in which this
     * tables are created is important for
     * binding constraints
     * thus consider having auto order for this tables
     */
    public static array $models = [
        "EntitiesModel",
        "ProjectsModel",
        "GroupsModel",
        "UsersModel"
    ];

    function onCreate()
    {
        $SQLs = static::generateModelsSql();

        foreach ($SQLs as $sql) {
            if (!$this->query($sql)) throw $this->exception;
        }

    }

    function onMissingDatabase($dbName)
    {
        $this->createDatabase($dbName);
    }

    function onConnectionFailed($host)
    {

    }

}