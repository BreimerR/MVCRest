<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/2/20
 * Time: 6:17 PM
 */


trait Common
{
    use WithDefault {
        appendToSql as withDefaultAppend;
    }

    public $maxSize = null;

    public ?bool $unique = null;

    public function __construct(string $name, int $maxSize = null, $default = null, bool $primary = false, bool $nullable = null, bool $unique = null)
    {
        parent::__construct($name, $primary, $default, $nullable);
        $this->maxSize = $maxSize;
        $this->unique = $unique;

    }

    protected function appendToSql(string $sql): string
    {
        $maxSize = $this->maxSize == null ? "" : " ({$this->maxSize})";
        $sql = $this->withDefaultAppend("$sql{$maxSize}");

        return $sql;
    }

}