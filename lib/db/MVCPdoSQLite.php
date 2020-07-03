<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/28/20
 * Time: 6:53 PM
 */

class MVCPdoSql extends MVCPdo
{

    function connect($configs): PDO
    {
        return new PDO("sqlite:{$configs['databaseFileSrc']}");
    }


    static function prepConfigs($host, $databaseName, $userName = "root", $password = "", $email = ""): array
    {
       return [

       ];
    }
}