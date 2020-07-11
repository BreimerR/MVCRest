<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/29/20
 * Time: 5:34 PM
 */

class Entity extends Table
{

    /**@return Column[] */
    static function initColumns(): array
    {
        return [
            new VarChar("name", 150, null),
            new Id("id", true)
        ];
    }

}