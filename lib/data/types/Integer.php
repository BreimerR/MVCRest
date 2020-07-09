<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/27/20
 * Time: 9:29 PM
 */

require_once "Common.php";

class Integer extends Number
{
    use Common;
}

class ID extends Integer
{
    public function __construct(string $name = "id", $primary = true, $default = null, $nullable = false)
    {
        parent::__construct($name, true, $default, $nullable);
    }
}