<?php

namespace Differ\MakerAST;

use function Functional\sort;

/**
 * @param mixed $decodedFirstFile
 * @param mixed $decodedSecondFile
 * @return mixed
 */
function makeAST(mixed $decodedFirstFile, mixed $decodedSecondFile): mixed
{
    $file1Keys = array_keys($decodedFirstFile);
    $file2Keys = array_keys($decodedSecondFile);
    $keys = array_unique(array_merge($file1Keys, $file2Keys));
    $sortedKeys = sort($keys, fn ($left, $right) => strcmp($left, $right));

    return array_map(callback: static function ($key) use ($decodedFirstFile, $decodedSecondFile) {
        $value1 = $decodedFirstFile[$key] ?? null;
        $value2 = $decodedSecondFile[$key] ?? null;

        if (is_array($value1) && is_array($value2)) {
            return ['status' => 'nested',
                'key' => $key,
                'value1' => makeAST($value1, $value2),
                'value2' => null];
        }

        if (!array_key_exists($key, $decodedFirstFile)) {
            return ['status' => 'added',
                'key' => $key,
//                'value1' => $value2,
                'value1' => getNestedNode($value2),
                'value2' => null];
        }

        if (!array_key_exists($key, $decodedSecondFile)) {
            return ['status' => 'deleted',
                'key' => $key,
//                'value1' => $value1,
                'value1' => getNestedNode($value1),
                'value2' => null];
        }

        if ($value1 !== $value2) {
            return ['status' => 'changed',
                'key' => $key,
//                'value1' => $value1,
//                'value2' => $value2];
                'value1' => getNestedNode($value1),
                'value2' => getNestedNode($value2)];
        }

        return ['status' => 'unchanged',
            'key' => $key,
            'value1' => $value1,
            'value2' => null];
    }, array: $sortedKeys);
}

/**
 * @param mixed $content
 * @return mixed
 */
function getNestedNode(mixed $content): mixed
{
    $iter = static function ($content) use (&$iter) {
        if (!is_array($content)) {
            return $content;
        }

        $keys = array_keys($content);
        return array_map(static function ($key) use ($content, $iter) {
            $value = is_array($content[$key]) ? $iter($content[$key]) : $content[$key];
            return ['status' => 'unchanged',
                'key' => $key,
                'value1' => $value,
                'value2' => null];
        }, $keys);
    };

    return $iter($content);
}
