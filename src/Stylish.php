<?php

namespace src\Stylish;

function stylish(mixed $mapped): string
{
    $string = implode("\n", $mapped);
    $result = '{' . "\n" . $string . "\n" . '}';
    print_r($result);
    return $result;
}
