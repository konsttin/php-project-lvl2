<?php

namespace Hexlet\Code\Differ;

function genDiff($firstFile, $secondFile)
{
    $file1 = json_decode($firstFile, true, 512, JSON_THROW_ON_ERROR);
    $file2 = json_decode($secondFile, true, 512, JSON_THROW_ON_ERROR);

    //$result = [];

    $merge = array_merge($file1, $file2);
    $keys = array_keys($merge);
    sort($keys);
    $mapped = array_map(callback: static function ($key) use ($merge, $file1, $file2) {
        if (is_bool($merge[$key])) {
            $merge[$key] = $merge[$key] ? 'true' : 'false';
        }
        if (!array_key_exists($key, $file2)) {
            return '- ' . $key . ': ' . $merge[$key];
        }
        if (!array_key_exists($key, $file1)) {
            return '+ ' . $key . ': ' . $merge[$key];
        }
        if (array_key_exists($key, $file1) && array_key_exists($key, $file2)) {
            if ($file1[$key] === $file2[$key]) {
                return '  ' . $key . ': ' . $merge[$key];
            }
            return '- ' . $key . ': ' . $file1[$key] . "\n " . '+ ' . $key . ': ' . $file2[$key];
        }
    }, array: $keys);
    $string = implode("\n", $mapped);
    return '{\n' . $string . '}';
}