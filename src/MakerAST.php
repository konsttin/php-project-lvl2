<?php

namespace Differ\MakerAST;

use function Functional\sort;

/**
 * @param mixed $decodedFirstFile
 * @param mixed $decodedSecondFile
 * @return array<mixed>
 */
function makeAST(mixed $decodedFirstFile, mixed $decodedSecondFile = false): array
{
    $merge = !is_array($decodedSecondFile) ? $decodedFirstFile : array_merge($decodedFirstFile, $decodedSecondFile);
    $keys = array_keys($merge);
    $sortedKeys = sort($keys, fn ($left, $right) => strcmp($left, $right));

    return array_map(callback: static function ($key) use ($decodedFirstFile, $decodedSecondFile) {
        if ($decodedSecondFile === false) {
//            if (is_array($decodedFirstFile[$key])) {
//                return ['status' => 'nested',
////                    'type' => 'node',
//                    'key' => $key,
//                    'value' => $decodedFirstFile[$key]];
//            }
            $value1 = stringify($decodedFirstFile[$key]);
            return ['status' => 'nested',
//                'type' => 'sheet',
                'key' => $key,
                'value1' => $value1];
        }

        if (!array_key_exists($key, $decodedSecondFile)) {
//            if (is_array($decodedFirstFile[$key])) {
//                return ['status' => 'deleted',
////                    'type' => 'node',
//                    'oldKey' => $key,
//                    'oldValue' => $decodedFirstFile[$key]];
//            }
            $value1 = stringify($decodedFirstFile[$key]);
            return ['status' => 'deleted',
//                'type' => 'sheet',
                'key' => $key,
                'value1' => $value1];
        }

        if (!array_key_exists($key, $decodedFirstFile)) {
//            if (is_array($decodedSecondFile[$key])) {
//                return ['status' => 'added',
////                    'type' => 'node',
//                    'newKey' => $key,
//                    'newValue' => $decodedSecondFile[$key]];
//            }
            $value2 = stringify($decodedSecondFile[$key]);
            return ['status' => 'added',
//                'type' => 'sheet',
                'key' => $key,
                'value2' => $value2];
        }

        if ($decodedFirstFile[$key] === $decodedSecondFile[$key]) {
//            if (is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
//                return ['status' => 'unchanged',
////                    'type' => 'node',
//                    'key' => $key,
//                    'children' => makeAST($decodedFirstFile[$key], $decodedSecondFile[$key])];
//            }
            $value = stringify($decodedFirstFile[$key]);
            return ['status' => 'unchanged',
//                'type' => 'sheet',
                'key' => $key,
                'value1' => $value];
        }

//        if (is_array($decodedFirstFile[$key]) && !is_array($decodedSecondFile[$key])) {
//            return ['status' => 'changed',
////                'oldType' => 'node',
////                'newType' => 'sheet',
//                'key' => $key,
//                'oldValue' => $decodedFirstFile[$key],
//                'newValue' => $decodedSecondFile[$key]];
//        }
//
//        if (!is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
//            return ['status' => 'changed',
////                'oldType' => 'sheet',
////                'newType' => 'node',
//                'key' => $key,
//                'oldValue' => $decodedFirstFile[$key],
//                'newValue' => $decodedSecondFile[$key]];
//        }

        if (is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
            return ['status' => 'changed',
//                'type' => 'node',
                'key' => $key,
                'children' => makeAST($decodedFirstFile[$key], $decodedSecondFile[$key])];
        }

        $value1 = stringify($decodedFirstFile[$key]);
        $value2 = stringify($decodedSecondFile[$key]);
        return ['status' => 'changed',
//            'type' => 'sheet',
            'key' => $key,
            'value1' => $value1,
            'value2' => $value2];
    }, array: $sortedKeys);
}

/**
 * @param mixed $content
 * @return mixed
 */
function stringify($content)
{
    $iter = function ($content) use (&$iter) {
        if (!is_array($content)) {
            if ($content === null) {
                return 'null';
            }
            return trim(var_export($content, true), "'");
        }

        $keys = array_keys($content);
        return array_map(function ($key) use ($content, $iter) {
            $value = (is_array($content[$key])) ? $iter($content[$key]) : $content[$key];

            return ['status' => 'unchanged', 'key' => $key, 'value1' => $value];
        }, $keys);
    };

    return $iter($content);
}
