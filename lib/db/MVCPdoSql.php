<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/28/20
 * Time: 6:53 PM
 */


abstract class MVCPdoSql extends MVCPdo
{

    function connect($settings): PDO
    {

        $conn = new PDO(
            "mysql:host={$settings['host']}",
            $settings['userName'],
            $settings['password']
        );

        return $conn;
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



