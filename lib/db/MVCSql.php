<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/28/20
 * Time: 2:13 PM
 */

class MVCSql extends MVCDatabase
{


    protected function __construct($settings = [])
    {
        $this->conn = mysqli_connect($settings["host"], $settings['userName'], $settings['password']);
        // Check connection
        if (!$this->conn) {
            /*TODO handle this better*/
            die("Connection failed: " . mysqli_connect_error());
        }
        echo "Connected successfully";
    }

    static function prepConfigs($host, $databaseName, $userName = "root", $password = "", $email = ""): array
    {
        return [
            "host" => $host,
            "databaseName" => $databaseName,
            "userName" => $userName,
            "password" => $password,
            "email" => $email,
        ];
    }
}