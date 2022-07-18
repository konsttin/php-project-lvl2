<?php

namespace Hexlet\Code;

function genDiff(string $firstFile, string $secondFile): string
{
    $contentOfFirstFile = file_get_contents(__DIR__ . '/../' . $firstFile);
    if ($contentOfFirstFile === false) {
        throw new \Exception();
    }

    $contentOfSecondFile = file_get_contents(__DIR__ . '/../' . $secondFile);
    if ($contentOfSecondFile === false) {
        throw new \Exception();
    }

    $decodedFirstFile = json_decode($contentOfFirstFile, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);
    $decodedSecondFile = json_decode($contentOfSecondFile, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);

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
    $string = implode("\n", $mapped);
    $result = '{' . "\n" . $string . "\n" . '}';
    print_r($result);
    return $result;
}
