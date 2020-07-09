<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/3/20
 * Time: 4:41 AM
 */

class User extends Table
{

    static function initColumns(): array
    {
        return [
            new Integer("id"),
            new VarChar("name", 150),
            self::registerForeignKey("entity", Entity::class)
        ];
    }
}