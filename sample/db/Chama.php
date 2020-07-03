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

$SQLs = Chama::generateModelsSql();
var_dump($SQLs);