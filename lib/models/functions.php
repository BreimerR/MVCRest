<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/2/20
 * Time: 5:56 AM
 */


function check_str_end(string $string, string $end, int $len, $endLength): bool
{
    return substr($string, $len - $endLength, $endLength) == $end;
}


function str_ends_with(string $string, string $end)
{
    return check_str_end($string, $end, strlen($string), strlen($end));
}

function str_rep_end(string $string, string $end, string $replacement): string
{
    $len = strlen($string);
    $eLen = strlen($end);

    if (check_str_end($string, $end, $len, $eLen)) {
        return substr($string, 0, $len - $eLen) . $replacement;
    }

    return $string;
}

function str_simple_replace_end($string, $len, $eLen, $replacement)
{
    return substr($string, 0, $len - $eLen) . $replacement;
}

function str_replace_end(string $string, string $replacement, $len, $eLen, $check_str_end)
{
    if ($check_str_end) {
        return substr($string, 0, $len - $eLen) . $replacement;
    }

    return $string;
}

function str_check_end_replace_with($string, $ends, $replacements, $len = null): string
{
    $len = $len == null ? strlen($string) : $len;

    foreach ($ends as $i => $end) {
        if (check_str_end($string, $end, $len, $eLen = strlen($end))) {
            return str_simple_replace_end($string, $len, $eLen, $replacements[$i]);
        }
    }

    return $string;

}
