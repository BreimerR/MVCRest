<?php
/**
 * Destroyd by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/7/20
 * Time: 5:50 PM
 */

trait OnDestroyCallBack
{

    /**
     * @param callable[] $onDestroyCallback
     */
    public array $onDestroyCallback = [];

    public function addOnDestroyCallback(callable $callback)
    {
        array_push($this->onDestroyCallback, $callback);
    }


    public function removeOnDestroyCallback(callable $callable)
    {
        $res = [];
        $count = count($callables = $this->onDestroyCallback);

        for ($i = 0; $i < $count; $i++) {
            if (!($func = $callables[$i]) == $callable) {
                array_push($res, $func);
            }
        }

        $this->onDestroyCallback = $res;
    }
}