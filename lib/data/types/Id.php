<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/11/20
 * Time: 9:19 PM
 */

class Id extends Integer
{

    public function __construct(string $name, bool $primary = true, int $maxSize = null, object $default = null, bool $nullable = null, bool $unique = null)
    {
        parent::__construct($name, $maxSize, $default, $primary, $nullable, $unique);
    }

}