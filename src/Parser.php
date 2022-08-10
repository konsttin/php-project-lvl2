<?php

namespace src\Parser;

function parser(mixed $decodedFirstFile, mixed $decodedSecondFile): string
{
    $merge = array_merge($decodedFirstFile, $decodedSecondFile);
    $keys = array_keys($merge);
    sort($keys);

    $diff = array_map(callback: static function ($key) use ($merge, $decodedFirstFile, $decodedSecondFile) {
        if (is_bool($merge[$key])) {
            $merge[$key] = $merge[$key] ? 'true' : 'false';
        }
        if (is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
            return ['node' =>
                ['status' => 'unchanged',
                'key' => $key,
                'value' => parser($decodedFirstFile[$key], $decodedSecondFile[$key])]
            ];
        }
        if ($decodedFirstFile[$key] === $decodedSecondFile[$key]) {
            return ['sheet' => ['status' => 'unchanged', 'key' => $key, 'value' => $merge[$key]]];
        }
        if (!array_key_exists($key, $decodedSecondFile)) {
            return ['sheet' => ['status' => 'removed', 'key' => $key, 'value' => $merge[$key]]];
        }
        if (!array_key_exists($key, $decodedFirstFile)) {
            return ['sheet' => ['status' => 'added', 'key' => $key, 'value' => $merge[$key]]];
        }
        return [
            ['sheet' => ['status' => 'removed', 'key' => $key, 'value' => $decodedFirstFile[$key]]],
            ['sheet' => ['status' => 'added', 'key' => $key, 'value' => $decodedSecondFile[$key]]]
        ];
    }, array: $keys);
    return stylish($diff);
}

function stylish(array $fileDiff): string
{
    $mapped = array_map(callback: static function ($key) use ($fileDiff) {
        if ($key === 'node') {
            return '    ' . $key . ': ' . stylish($fileDiff[$key]);
        }
        if (key($fileDiff) === 'sheet' && $fileDiff[$key][1] = 'removed') {
            return '  - ' . $key . ': ' . $fileDiff[$key];
        }
        if (key($fileDiff) === 'sheet' && $fileDiff[$key][1] = 'added') {
            return '  + ' . $key . ': ' . $fileDiff[$key];
        }
        return '    ' . $key . ': ' . $fileDiff[$key];
    }, array: $fileDiff);

    $string = implode("\n", $mapped);
    $result = '{' . "\n" . $string . "\n" . '}';
    print_r($result);
    return $result;
}
