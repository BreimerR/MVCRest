<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/8/20
 * Time: 1:44 AM
 */

class Json
{

    public static function parse($json = [])
    {
        return json_encode($json);
    }

    public static function toArray($json)
    {
        return json_decode($json, true);
    }

    public static function string($array = array())
    {
        return self::parse($array);
    }
}