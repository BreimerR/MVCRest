<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/27/20
 * Time: 9:29 PM
 */

class Text extends Type
{

    protected function appendToSql(string $sql): string
    {
        return $sql;
    }
}