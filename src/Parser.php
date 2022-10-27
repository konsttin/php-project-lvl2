<?php

namespace src\Parser;

function parser(mixed $decodedFirstFile, mixed $decodedSecondFile): string
{
    $result = diff($decodedFirstFile, $decodedSecondFile);
    return stylish($result);
}

function stylish(array $fileDiff): string
{
    $iter = static function (array $node, int $depth) use (&$iter) {
        $mapped = array_map(static function ($value) use ($iter, $depth) {
            $indent = str_repeat('  ', $depth);
            $indent2 = str_repeat('  ', $depth - 1);

            if ($value['status'] === 'unchanged' || $value['status'] === 'nested') {
                if ($value['type'] === 'node') {
                    return "$indent$indent{$value['key']}: {$iter($value['children'], $depth + 1)}";
                }
                return "$indent$indent{$value['key']}: {$value['value']}";
            }

            if ($value['status'] === 'deleted') {
                if ($value['type'] === 'node') {
                    return "$indent$indent2- {$value['oldKey']}: {$iter($value['children'], $depth + 1)}";
                }
                return "$indent$indent2- {$value['oldKey']}: {$value['oldValue']}";
            }

            if ($value['status'] === 'added') {
                if ($value['type'] === 'node') {
                    return "$indent$indent2+ {$value['newKey']}: {$iter($value['children'], $depth + 1)}";
                }
                return "$indent$indent2+ {$value['newKey']}: {$value['newValue']}";
            }

            if ($value['status'] === 'changed') {
                if (!empty($value['oldType'])) {
                    return $indent . $indent2 . "- " . $value['key'] . ": " . $iter($value['oldChildren'], $depth + 1) . "\n" .
                        $indent . $indent2 . "+ " . $value['key'] . ": " . $value['newValue'];
                }

                if (!empty($value['newType'])) {
                    return $indent . $indent2 . "- " . $value['key'] . ": " . $value['oldValue'] . "\n" .
                        $indent . $indent2 . "+ " . $value['key'] . ": " . $iter($value['newChildren'], $depth + 1);
                }

                if ($value['type'] === 'node') {
                    return "$indent$indent{$value['key']}: {$iter($value['children'], $depth + 1)}";
                }
            }

            return $indent . $indent2 . "- " . $value['key'] . ": " . $value['oldValue'] . "\n" .
                $indent . $indent2 . "+ " . $value['key'] . ": " . $value['newValue'];

        }, $node);
        //print_r($mapped);
        $string = implode("\n", $mapped);
        $bracketIndent = str_repeat('  ', ($depth - 1) * 2);
        return '{' . "\n" . $string . "\n" . $bracketIndent . '}';
    };

    return $iter($fileDiff, 1);
}

function toString($value): string
{
    return strtolower(trim(var_export($value, true), "'"));
}

function diff($decodedFirstFile, $decodedSecondFile = false): array
{
    $merge = !is_array($decodedSecondFile) ? $decodedFirstFile : array_merge($decodedFirstFile, $decodedSecondFile);
    $keys = array_keys($merge);
    sort($keys);
    //print_r($keys);
    $result = array_map(callback: static function ($key) use ($decodedFirstFile, $decodedSecondFile) {
        if ($decodedSecondFile === false) {
            if (is_array($decodedFirstFile[$key])) {
                return ['status' => 'nested',
                    'type' => 'node',
                    'key' => $key,
                    'children' => diff($decodedFirstFile[$key])];
            }
            $decodedFirstFile[$key] = is_string($decodedFirstFile[$key]) ? $decodedFirstFile[$key] : toString($decodedFirstFile[$key]);
            return ['status' => 'nested',
                'type' => 'sheet',
                'key' => $key,
                'value' => $decodedFirstFile[$key]];
        }

        if (!array_key_exists($key, $decodedSecondFile)) {
            if (is_array($decodedFirstFile[$key])) {
                return ['status' => 'deleted',
                    'type' => 'node',
                    'oldKey' => $key,
                    'children' => diff($decodedFirstFile[$key])];
            }
            $decodedFirstFile[$key] = is_string($decodedFirstFile[$key]) ? $decodedFirstFile[$key] : toString($decodedFirstFile[$key]);
            return ['status' => 'deleted',
                'type' => 'sheet',
                'oldKey' => $key,
                'oldValue' => $decodedFirstFile[$key]];
        }

        if (!array_key_exists($key, $decodedFirstFile)) {
            if (is_array($decodedSecondFile[$key])) {
                return ['status' => 'added',
                    'type' => 'node',
                    'newKey' => $key,
                    'children' => diff($decodedSecondFile[$key])];
            }
            $decodedSecondFile[$key] = is_string($decodedSecondFile[$key]) ? $decodedSecondFile[$key] : toString($decodedSecondFile[$key]);
            return ['status' => 'added',
                'type' => 'sheet',
                'newKey' => $key,
                'newValue' => $decodedSecondFile[$key]];
        }

        if ($decodedFirstFile[$key] === $decodedSecondFile[$key]) {
            if (is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
                return ['status' => 'unchanged',
                    'type' => 'node',
                    'key' => $key,
                    'children' => diff($decodedFirstFile[$key], $decodedSecondFile[$key])];
            }
            $decodedFirstFile[$key] = is_string($decodedFirstFile[$key]) ? $decodedFirstFile[$key] : toString($decodedFirstFile[$key]);
            return ['status' => 'unchanged',
                'type' => 'sheet',
                'key' => $key,
                'value' => $decodedFirstFile[$key]];
        }

        if (is_array($decodedFirstFile[$key]) && !is_array($decodedSecondFile[$key])) {
            return ['status' => 'changed',
                'oldType' => 'node',
                'newType' => 'sheet',
                'key' => $key,
                'oldChildren' => diff($decodedFirstFile[$key]),
                'newValue' => $decodedSecondFile[$key]];
        }

        if (!is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
            return ['status' => 'changed',
                'oldType' => 'sheet',
                'newType' => 'node',
                'key' => $key,
                'oldValue' => $decodedFirstFile[$key],
                'newChildren' => diff($decodedSecondFile[$key])];
        }

        if (is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
            return ['status' => 'changed',
                'type' => 'node',
                'key' => $key,
                'children' => diff($decodedFirstFile[$key], $decodedSecondFile[$key])];
        }

        $decodedFirstFile[$key] = is_string($decodedFirstFile[$key]) ? $decodedFirstFile[$key] : toString($decodedFirstFile[$key]);
        $decodedSecondFile[$key] = is_string($decodedSecondFile[$key]) ? $decodedSecondFile[$key] : toString($decodedSecondFile[$key]);

        return ['status' => 'changed',
            'type' => 'sheet',
            'key' => $key,
            'oldValue' => $decodedFirstFile[$key],
            'newValue' => $decodedSecondFile[$key]];
    }, array: $keys);
    //print_r($result);
    return $result;
}
