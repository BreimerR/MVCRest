<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/3/20
 * Time: 5:06 AM
 */

class Group extends Table
{
    static function initColumns(): array
    {
        return [
            new Id("entity"),

        ];
    }
}