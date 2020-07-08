<?php

require_once "../../lib/db/MVCPdoSql.php";
require_once "../models/ProjectsModel.php";
require_once "../models/EntitiesModel.php";
require_once "../models/UsersModel.php";
require_once "../models/GroupsModel.php";

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


}