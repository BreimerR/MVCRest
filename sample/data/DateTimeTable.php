<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/8/20
 * Time: 3:28 PM
 */

class DateTimeTable extends Table
{

    static function initColumns(): array
    {
        return [
            new Id(),
            new DateTimeColumn()
        ];
    }
}