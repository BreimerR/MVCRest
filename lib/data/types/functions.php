<?php

function array_filter_map(array $array, callable $callable)
{
    $res = [];

    foreach ($array as $arrayItem) {
        $newValue = $callable($arrayItem);
        if ($newValue != null) array_push($res, $newValue);
    }

    return $res;
}