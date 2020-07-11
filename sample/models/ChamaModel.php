<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/9/20
 * Time: 4:05 PM
 */


class ChamaModel extends Model
{

    function getConnection()
    {
        return Chama::getInstance();
    }
}