<?php

namespace Hexlet\Code;

function genDiff(string $firstFile, string $secondFile): string
{
    $contentOfFirstFile = file_get_contents(__DIR__ . '/../' . $firstFile);
    $contentOfSecondFile = file_get_contents(__DIR__ . '/../' . $secondFile);

    try {
        $file1 = json_decode(json: $contentOfFirstFile, associative: true, depth: 512, flags: JSON_THROW_ON_ERROR);
    } catch (\JsonException $e) {
    }
    try {
        $file2 = json_decode(json: $contentOfSecondFile, associative: true, depth: 512, flags: JSON_THROW_ON_ERROR);
    } catch (\JsonException $e) {
    }

    $merge = array_merge($file1, $file2);
    $keys = array_keys($merge);
    sort($keys);

    $mapped = array_map(callback: static function ($key) use ($merge, $file1, $file2) {
        if (is_bool($merge[$key])) {
            $merge[$key] = $merge[$key] ? 'true' : 'false';
        }
        if (!array_key_exists($key, $file2)) {
            return '  - ' . $key . ': ' . $merge[$key];
        }
        if (!array_key_exists($key, $file1)) {
            return '  + ' . $key . ': ' . $merge[$key];
        }
        if ($file1[$key] === $file2[$key]) {
            return '    ' . $key . ': ' . $merge[$key];
        }
        return '  - ' . $key . ': ' . $file1[$key] . "\n" . '  + ' . $key . ': ' . $file2[$key];
    }, array: $keys);
    $string = implode("\n", $mapped);
    $result = '{' . "\n" . $string . "\n" . '}';
    print_r($result);
    return $result;
}
