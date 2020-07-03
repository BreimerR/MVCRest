<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/27/20
 * Time: 9:42 PM
 */

require_once "WithDefault.php";

class Date extends Type
{


    private bool $unique;
    /**TODO
     * need to add a check action in the sql to check if
     * this date is greater than the max value
     */
    private string $maxSize;

    use WithDefault {
        appendToSql as withDefaultAppend;
    }

    public function __construct($name, $maxSize, $primary = false, string $default = null, $nullable = false, $unique = false)
    {
        parent::__construct($name, $primary, $default, $nullable);
        $this->maxSize = $maxSize;
        $this->unique = $unique;

    }

    /**TODO
     * use the same format from your app for this section can be passed
     * from the controller to make this sensible
     * @param string $format
     * @return false|string
     */
    static function now(string $format = "Y m d"): string
    {
        return date($format);
    }

    function appendToSql(string $sql): string
    {

        return $this->withDefaultAppend($sql);
    }

}