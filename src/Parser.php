<?php

namespace src\Parser;

use function src\Stylish\stylish;

function parser(mixed $decodedFirstFile, mixed $decodedSecondFile): string
{
    $merge = array_merge($decodedFirstFile, $decodedSecondFile);
    $keys = array_keys($merge);
    sort($keys);

    $mapped = array_map(callback: static function ($key) use ($merge, $decodedFirstFile, $decodedSecondFile) {
        if (is_bool($merge[$key])) {
            $merge[$key] = $merge[$key] ? 'true' : 'false';
        }
        if (!array_key_exists($key, $decodedSecondFile)) {
            return '  - ' . $key . ': ' . $merge[$key];
        }
        if (!array_key_exists($key, $decodedFirstFile)) {
            return '  + ' . $key . ': ' . $merge[$key];
        }
        if ($decodedFirstFile[$key] === $decodedSecondFile[$key]) {
            return '    ' . $key . ': ' . $merge[$key];
        }
        return '  - ' . $key . ': ' . $decodedFirstFile[$key] . "\n" . '  + ' . $key . ': ' . $decodedSecondFile[$key];
    }, array: $keys);
    return stylish($mapped);
}
