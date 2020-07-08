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
    protected PDO $conn;

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


}



