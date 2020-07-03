<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/27/20
 * Time: 9:41 PM
 */

class DateTime extends Date
{

    static function now(string $format = "Y m d h:m:i:sA"): string
    {
        return parent::now($format);
    }
}