<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/27/20
 * Time: 9:29 PM
 */


require_once "Column.php";

/**
 * CREATE TABLE users(
 *      name VARCHAR(size) UNIQUE DEFAULT "" COLLATION ATTRIBUTES NULLABLE AUTO_INCREMENT Virtuality
 * );
 */
require_once "Common.php";

class VarChar extends Column
{
    /*col_name VARCHAR(200) PRIMARY_KEY*/
    use Common {
        appendToSql as traitAppendSql;
    }

    public string $name;
    public bool $primary = false;
    public bool $unique = false;

    public function __construct($name, $maxSize = null, string $default = null, $primary = false, $nullable = null, $unique = false)
    {
        parent::__construct($name, $primary, $default, $nullable);
        $this->maxSize = $maxSize;
        $this->unique = $unique;

    }

    protected function appendToSql(string $sql): string
    {
        return $this->traitAppendSql($sql);
    }



}