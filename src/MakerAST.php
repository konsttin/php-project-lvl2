<?php

namespace Differ\MakerAST;

use function Functional\sort;

function makeAST(mixed $decodedFirstFile, mixed $decodedSecondFile = false): mixed
{
    $merge = !is_array($decodedSecondFile) ? $decodedFirstFile : array_merge($decodedFirstFile, $decodedSecondFile);
    $keys = array_keys($merge);
    $sortedKeys = sort($keys, fn($left, $right) => strcmp($left, $right));

    return array_map(callback: static function ($key) use ($decodedFirstFile, $decodedSecondFile) {
        if ($decodedSecondFile === false) {
            if (is_array($decodedFirstFile[$key])) {
                return ['status' => 'nested',
//                    'type' => 'node',
                    'key' => $key,
                    'value1' => makeAST($decodedFirstFile[$key])];
            }

            return ['status' => 'nested',
//                'type' => 'sheet',
                'key' => $key,
                'value1' => $decodedFirstFile[$key]];
//            return getNestedNode($decodedFirstFile[$key], $key);
        }

        if (!array_key_exists($key, $decodedSecondFile)) {
            if (is_array($decodedFirstFile[$key])) {
                return ['status' => 'deleted',
//                    'type' => 'node',
                    'key' => $key,
                    'value1' => makeAST($decodedFirstFile[$key])];
            }

            return ['status' => 'deleted',
//                'type' => 'sheet',
                'key' => $key,
                'value1' => $decodedFirstFile[$key]];
        }

        if (!array_key_exists($key, $decodedFirstFile)) {
            if (is_array($decodedSecondFile[$key])) {
                return ['status' => 'added',
//                    'type' => 'node',
                    'key' => $key,
                    'value2' => makeAST($decodedSecondFile[$key])];
            }

            return ['status' => 'added',
//                'type' => 'sheet',
                'key' => $key,
                'value2' => $decodedSecondFile[$key]];
        }

        if ($decodedFirstFile[$key] === $decodedSecondFile[$key]) {
            if (is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
                return ['status' => 'unchanged',
//                    'type' => 'node',
                    'key' => $key,
                    'value1' => makeAST($decodedFirstFile[$key], $decodedSecondFile[$key])];
            }

            return ['status' => 'unchanged',
//                'type' => 'sheet',
                'key' => $key,
                'value1' => $decodedFirstFile[$key]];
        }

        if (is_array($decodedFirstFile[$key]) && !is_array($decodedSecondFile[$key])) {
            return ['status' => 'changed',
//                'oldType' => 'node',
//                'newType' => 'sheet',
                'key' => $key,
                'value1' => makeAST($decodedFirstFile[$key]),
                'value2' => $decodedSecondFile[$key]];
        }

        if (!is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
            return ['status' => 'changed',
//                'oldType' => 'sheet',
//                'newType' => 'node',
                'key' => $key,
                'value1' => $decodedFirstFile[$key],
                'value2' => makeAST($decodedSecondFile[$key])];
        }

        if (is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
            return ['status' => 'changed',
//                'type' => 'node',
                'key' => $key,
                'value1' => makeAST($decodedFirstFile[$key], $decodedSecondFile[$key])];
        }

        return ['status' => 'changed',
//            'type' => 'sheet',
            'key' => $key,
            'value1' => $decodedFirstFile[$key],
            'value2' => $decodedSecondFile[$key]];
    }, array: $sortedKeys);
}


/**
 * @param mixed $content
 * @param mixed $key
 * @return mixed
 */
function getNestedNode($content, $key)
{
    $iter = function ($node) use (&$iter, $key) {
        if (!is_array($node)) {
            return ['status' => 'nested',
                'type' => 'sheet',
                'key' => $key,
                'value' => $node];
        }

        $keys = array_keys($node);
        return array_map(function ($key) use ($node, $iter) {
            $value = is_array($node[$key]) ? $iter($node[$key]) : $node[$key];

            return ['status' => 'nested',
                'type' => 'node',
                'key' => $key,
                'children' => $value];
        }, $keys);
    };

    return $iter($content);
}
