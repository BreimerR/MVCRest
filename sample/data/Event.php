<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/8/20
 * Time: 3:26 PM
 */

class Event extends Table
{

    static function initColumns(): array
    {
        return [
            new ID(),
            new VarChar("name", 150),
            self::registerForeignKey("entity", Entity::class, null, ["onPreInsert" => function ($instance) {
                // insert the date to the database
            }]),
        ];
    }
}