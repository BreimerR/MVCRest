<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/2/20
 * Time: 7:03 PM
 */

trait WithDefault
{
    // false defined,false undefined, true defined true undefined
    protected function appendToSql(string $sql): string
    {
        $default = $this->default == null ? $this->default : (is_int($this->default) ? $this->default : "'{$this->default}'");

        $default = $this->nullable ? ($default == null ? "NULL" : $default) : $default;
        $default = $default != null && $default != "" ? "DEFAULT $default" : $default;

        return "$sql $default";
    }
}