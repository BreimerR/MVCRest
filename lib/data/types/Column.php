<?php

/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/26/20
 * Time: 7:25 PM
 */
abstract class Column
{

    public string $name;
    public $default = "";
    public $nullable = false;
    protected bool $primary = false;

    public function __construct(string $name, bool $primary = false, $default = null, bool $nullable = null)
    {
        $this->name = $name;
        $this->primary = $primary;
        $this->default = $default;
        $this->nullable = $nullable;
    }

    static function create(...$params)
    {
        return new static(...$params);
    }

    public function __toString()
    {
        return strtoupper(static::class);
    }


    public function isPrimary()
    {
        return $this->primary;
    }

    function getSql(): string
    {
        $name = $this->name;

        $sql = $this->appendToSql("\n\t`$name` $this");

        if ($this->primary) {
            $sql .= ",\nPRIMARY KEY (`$name`)";
        }

        return $sql;
    }

    protected function appendToSql(string $sql): string
    {
        return $sql;
    }

    function copy(...$params)
    {
        return new static(...$params);
    }


}