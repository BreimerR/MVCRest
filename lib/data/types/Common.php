<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/2/20
 * Time: 6:17 PM
 */

require_once "WithDefault.php";

trait Common
{
    use WithDefault {
        appendToSql as withDefaultAppend;
    }

    public
        $maxSize = null;

    protected function appendToSql(string $sql): string
    {
        $maxSize = $this->maxSize == null ? "" : " ({$this->maxSize})";
        $sql = $this->withDefaultAppend("$sql{$maxSize}");

        return $sql;
    }

}