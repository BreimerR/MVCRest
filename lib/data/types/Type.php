<?php

/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 6/26/20
 * Time: 7:25 PM
 */
abstract class Type
{

    public string $name;
    public $default = "";
    public $nullable = false;
    protected bool $primary = false;

    public function __construct(string $name, $primary = false, $default = null, $nullable = false)
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
        return $this->appendToSql("\n\t{$this->name} $this");
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