<?php

namespace src\Parser;

function parser(mixed $decodedFirstFile, mixed $decodedSecondFile): string
{
    $merge = array_merge($decodedFirstFile, $decodedSecondFile);
    $keys = array_keys($merge);
    sort($keys);

//    $diff = fn($keys) => array_map(callback: static function ($key) use (&$diff, $merge, $decodedFirstFile, $decodedSecondFile) {
//        if (!is_string($merge[$key])) {
//            $merge[$key] = trim(var_export($merge[$key], true), "'");
//        }
//
//        if (!array_key_exists($key, $decodedSecondFile)) {
//            if (is_array($decodedFirstFile[$key])) {
//                return ['changed' =>
//                    ['type' => 'node', 'oldKey' => $key, 'children' => $diff($merge[$key])]];
//            }
//            return ['changed' => ['type' => 'sheet', 'oldKey' => $key, 'oldValue' => $merge[$key]]];
//        }
//
//        if (!array_key_exists($key, $decodedFirstFile)) {
//            if (is_array($decodedSecondFile[$key])) {
//                return ['changed' =>
//                    ['type' => 'node', 'newKey' => $key, 'children' => $diff($merge[$key])]];
//            }
//            return ['changed' => ['type' => 'sheet', 'newKey' => $key, 'newValue' => $merge[$key]]];
//        }
//
//        if ($decodedFirstFile[$key] === $decodedSecondFile[$key]) {
//            if (is_array($decodedFirstFile[$key])) {
//                return ['unchanged' => ['type' => 'node', 'key' => $key, 'children' => $diff($merge[$key])]];
//            }
//            return ['unchanged' => ['type' => 'sheet', 'key' => $key, 'value' => $merge[$key]]];
//        }
//
//        if (is_array($merge[$key])) {
//            return ['changed' => ['type' => 'node', 'key' => $key, 'children' => $diff($merge[$key])]];
//        }
//
//        return ['changed' =>
//            ['type' => 'sheet',
//                'key' => $key,
//                'oldValue' => $decodedFirstFile[$key],
//                'newValue' => $decodedSecondFile[$key]]];
//    }, array: $keys);

    //$result = $diff($keys);
    //print_r($result);
    $result = diff($keys, $merge, $decodedFirstFile, $decodedSecondFile);
    return stylish($result);
}

function stylish(array $fileDiff): string
{
    $iter = static function (array $node, int $depth) use (&$iter) {
        $mapped = array_map(static function ($value) use ($iter, $depth) {
            $indent = str_repeat('  ', $depth);

            if (isset($value['unchanged'])) {
                if ($value['unchanged']['type'] === 'node') {
                    return "{$indent}{$indent}{$value['unchanged']['key']}: {$iter($value, $depth + 1)}";
                }
                return "{$indent}{$indent}{$value['unchanged']['key']}: {$value['unchanged']['value']}";
            }
            if (isset($value['changed']['oldKey']) && !isset($value['changed']['newKey'])) {
                return "{$indent}- {$value['changed']['oldKey']}: {$value['changed']['oldValue']}";
            }
            if (isset($value['changed']['oldValue'], $value['changed']['newValue'])) {
                return $indent . "- " . $value['changed']['key'] . ": " . $value['changed']['oldValue'] . "\n" .
                    $indent . "+ " . $value['changed']['key'] . ": " . $value['changed']['newValue'];
            }
            return "{$indent}+ {$value['changed']['newKey']}: {$value['changed']['newValue']}";
        }, $node);
        //print_r($mapped);
        $string = implode("\n", $mapped);
        return '{' . "\n" . $string . "\n" . '}';
    };

    return $iter($fileDiff, 1);
}

function diff($keys, $merge, $decodedFirstFile, $decodedSecondFile): array
{
    return array_map(callback: static function ($key) use ($merge, $decodedFirstFile, $decodedSecondFile) {
        if (!is_string($merge[$key])) {
            $merge[$key] = trim(var_export($merge[$key], true), "'");
        }

        if (!array_key_exists($key, $decodedSecondFile)) {
            if (is_array($decodedFirstFile[$key])) {
                return ['changed' =>
                    ['type' => 'node', 'oldKey' => $key, 'children' => diff($merge[$key], $merge, $decodedFirstFile, $decodedSecondFile)]];
            }
            return ['changed' => ['type' => 'sheet', 'oldKey' => $key, 'oldValue' => $merge[$key]]];
        }

        if (!array_key_exists($key, $decodedFirstFile)) {
            if (is_array($decodedSecondFile[$key])) {
                return ['changed' =>
                    ['type' => 'node', 'newKey' => $key, 'children' => diff($merge[$key], $merge, $decodedFirstFile, $decodedSecondFile)]];
            }
            return ['changed' => ['type' => 'sheet', 'newKey' => $key, 'newValue' => $merge[$key]]];
        }

        if ($decodedFirstFile[$key] === $decodedSecondFile[$key]) {
            if (is_array($decodedFirstFile[$key])) {
                return ['unchanged' => ['type' => 'node', 'key' => $key, 'children' => diff($merge[$key], $merge, $decodedFirstFile, $decodedSecondFile)]];
            }
            return ['unchanged' => ['type' => 'sheet', 'key' => $key, 'value' => $merge[$key]]];
        }

        if (is_array($merge[$key])) {
            return ['changed' => ['type' => 'node', 'key' => $key, 'children' => diff($merge[$key], $merge, $decodedFirstFile, $decodedSecondFile)]];
        }

        return ['changed' =>
            ['type' => 'sheet',
                'key' => $key,
                'oldValue' => $decodedFirstFile[$key],
                'newValue' => $decodedSecondFile[$key]]];
    }, array: $keys);
}
