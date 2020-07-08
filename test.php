<?php


function test($name)
{
    switch ($name) {
        case "breimer" || "jael":
            echo $name;

            break;

        case "chris":
            echo "No nae";

    }
}

test("chris");