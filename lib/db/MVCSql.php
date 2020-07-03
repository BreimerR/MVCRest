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


    public function __construct($settings = [])
    {
        $this->conn = mysqli_connect($settings["host"], $settings['userName'], $settings['password']);
        // Check connection
        if (!$this->conn) {
            /*TODO handle this better*/
            die("Connection failed: " . mysqli_connect_error());
        }
        echo "Connected successfully";
    }
}