<?php


interface AppController
{

    public function __construct($data = array());

    public function __call($func, $args);

}