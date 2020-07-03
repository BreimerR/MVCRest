<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/26/20
 * Time: 7:35 PM
 */

class Column
{
    public $value;

    /**
     * The default value can be an action or a separate sql query
     * this might help reduce amount of data you store in the database
     * @param $name
     * @param $type
     * @param object $default
     */
    public function __construct($name, $type, $default = null)
    {

        $this->name = $name;
        $this->type = $type;
        $value = $default;
    }

}