<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/28/20
 * Time: 1:56 PM
 */

require_once "MVCDatabase.php";

abstract class MVCPdo extends MVCDatabase
{

    /**
     * @param PDO $conn
     */
    protected $conn;


    abstract function connect($configs): PDO;

    public function __construct($settings = [])
    {
        try {
            $this->conn = $this->connect($settings);
        } catch (PDOException $e) {
            /*TODO handle this better*/
            echo $e->getMessage();
        }
    }

}