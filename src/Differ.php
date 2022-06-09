<?php

namespace Hexlet\Code\Differ;

function genDiff($firstFile, $secondFile)
{
    $file1 = json_decode($firstFile, true, 512, JSON_THROW_ON_ERROR);
    $file2 = json_decode($secondFile, true, 512, JSON_THROW_ON_ERROR);

    //$result = [];

    $merge = array_merge($file1, $file2);
    $keys = array_keys($merge);

    $mapped = array_map(callback: static function ($key) use ($merge, $file1, $file2) {
        if (!array_key_exists($key, $file2)) {
            if (is_bool($merge[$key])) {
                $merge[$key] = $merge[$key] ? 'true' : 'false';
            }
            return '- ' . $key . ': ' . $merge[$key];
        }
        if (!array_key_exists($key, $file1)) {
            if (is_bool($merge[$key])) {
                $merge[$key] = $merge[$key] ? 'true' : 'false';
            }
            return '+ ' . $key . ': ' . $merge[$key];
        }
        if (array_key_exists($key, $file1) && array_key_exists($key, $file2)) {
            if (is_bool($merge[$key])) {
                $merge[$key] = $merge[$key] ? 'true' : 'false';
            }
            return '  ' . $key . ': ' . $merge[$key];
        }
        if (array_key_exists($key, $file1) && array_key_exists($key, $file2) && $file1[$key] !== $file2[$key]) {
            if (is_bool($file1[$key])) {
                $file1[$key] = $file1[$key] ? 'true' : 'false';
            }
            if (is_bool($file2[$key])) {
                $file2[$key] = $file2[$key] ? 'true' : 'false';
            }
            return '- ' . $key . ': ' . $file1[$key] . "\n " . '+ ' . $key . ': ' . $file2[$key];
        }
    }, array: $keys);
    return $mapped;
}