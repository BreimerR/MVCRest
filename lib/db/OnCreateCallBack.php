<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/7/20
 * Time: 5:50 PM
 */

trait OnCreateCallBack
{

    /**
     * @param callable[] $onCreateCallback
     */
    public array $onCreateCallback = [];

    public function addOnCreateCallback(callable $callback)
    {
        array_push($this->onCreateCallback, $callback);
    }


    public function removeOnCreateCallback(callable $callable)
    {
        $res = [];
        $count = count($callables = $this->onCreateCallback);

        for ($i = 0; $i < $count; $i++) {
            if (!($func = $callables[$i]) == $callable) {
                array_push($res, $func);
            }
        }

        $this->onCreateCallback = $res;
    }
}