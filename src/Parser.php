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

            if (isset($value['unchanged'])) {
                if ($value['unchanged']['type'] === 'node') {
                    return "{$indent}{$indent}{$value['unchanged']['key']}: {$iter($value, $depth + 1)}";
                }
                return "{$indent}{$indent}{$value['unchanged']['key']}: {$value['unchanged']['value']}";
            }

            if (isset($value['changed']['oldKey']) && !isset($value['changed']['newKey'])) {
                if ($value['changed']['type'] === 'node') {
                    return $indent . "- " . $value['changed']['oldKey'] . ": " . stringify($value['changed']['children']);
                }
                return "{$indent}- {$value['changed']['oldKey']}: {$value['changed']['oldValue']}";
            }

            if (isset($value['changed']['oldValue'], $value['changed']['newValue'])) {
                $value['changed']['oldValue'] = is_array($value['changed']['oldValue']) ? stringify($value['changed']['oldValue'], $indent) : $value['changed']['oldValue'];
                $value['changed']['newValue'] = is_array($value['changed']['newValue']) ? stringify($value['changed']['newValue'], $indent) : $value['changed']['newValue'];
                return $indent . "- " . $value['changed']['key'] . ": " . $value['changed']['oldValue'] . "\n" .
                    $indent . "+ " . $value['changed']['key'] . ": " . $value['changed']['newValue'];
            }

            if ($value['changed']['type'] === 'node') {
                return $indent . "+ " . key($value) . ": " . stringify($value['changed']['children']);
            }

            return "{$indent}+ {$value['changed']['newKey']}: {$value['changed']['newValue']}";
        }, $node);
        //print_r($mapped);
        $string = implode("\n", $mapped);
        return '{' . "\n" . $string . "\n" . '}';
    };

    return $iter($fileDiff, 1);
}

function stringify($data, $replacer = ' ', $spacesCount = 1): string
{
    return iter($data, $replacer, $spacesCount);
}

function iter($node, $replacer, $spacesCount, $depth = 1): string
{
    if (!is_array($node)) {
        return toString($node);
    }

    $children = array_map(function ($child, $key) use ($replacer, $spacesCount, $depth) {
        $indent = str_repeat($replacer, $spacesCount * $depth);
        if (is_array($child)) {
            $iteration = iter($child, $replacer, $spacesCount, $depth + 1);
            return "{$indent}{$key}: {$iteration}";
        }
        $value = toString($child);
        return "{$indent}{$key}: {$value}";
    }, $node, array_keys($node));

    $arrayToString = implode("\n", $children);
    $bracketIndent = str_repeat($replacer, $spacesCount * ($depth - 1));
    return "{\n{$arrayToString}\n{$bracketIndent}}";
}

function toString($value): string
{
    return trim(var_export($value, true), "'");
}

function diff($decodedFirstFile, $decodedSecondFile): array
{
    $merge = array_merge($decodedFirstFile, $decodedSecondFile);
    $keys = array_keys($merge);
    sort($keys);
    //print_r($keys);
    $result = array_map(callback: static function ($key) use ($decodedFirstFile, $decodedSecondFile) {
        if (!array_key_exists($key, $decodedSecondFile)) {
            if (is_array($decodedFirstFile[$key])) {
                return ['changed' =>
                    [
                        'type' => 'node',
                        'oldKey' => $key,
                        'children' => $decodedFirstFile[$key]
                    ]];
            }
            $decodedFirstFile[$key] = is_string($decodedFirstFile[$key]) ? $decodedFirstFile[$key] : toString($decodedFirstFile[$key]);
            return ['changed' => ['type' => 'sheet', 'oldKey' => $key, 'oldValue' => $decodedFirstFile[$key]]];
        }

        if (!array_key_exists($key, $decodedFirstFile)) {
            if (is_array($decodedSecondFile[$key])) {
                return ['changed' =>
                    ['type' => 'node',
                        'newKey' => $key,
                        'children' => $decodedSecondFile[$key]]
                ];
            }
            $decodedSecondFile[$key] = is_string($decodedSecondFile[$key]) ? $decodedSecondFile[$key] : toString($decodedSecondFile[$key]);
            return ['changed' => ['type' => 'sheet', 'newKey' => $key, 'newValue' => $decodedSecondFile[$key]]];
        }

        if ($decodedFirstFile[$key] === $decodedSecondFile[$key]) {
            if (is_array($decodedFirstFile[$key])) {
                return ['unchanged' =>
                    ['type' => 'node',
                        'key' => $key,
                        'children' => diff($decodedFirstFile[$key], $decodedSecondFile[$key])]
                ];
            }
            $decodedFirstFile[$key] = is_string($decodedFirstFile[$key]) ? $decodedFirstFile[$key] : toString($decodedFirstFile[$key]);
            return ['unchanged' => ['type' => 'sheet', 'key' => $key, 'value' => $decodedFirstFile[$key]]];
        }

        if (is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
            return ['changed' =>
                ['type' => 'node',
                    'key' => $key,
                    'children' => diff($decodedFirstFile[$key], $decodedSecondFile[$key])]
            ];
        }

        $decodedFirstFile[$key] = is_string($decodedFirstFile[$key]) ? $decodedFirstFile[$key] : toString($decodedFirstFile[$key]);
        $decodedSecondFile[$key] = is_string($decodedSecondFile[$key]) ? $decodedSecondFile[$key] : toString($decodedSecondFile[$key]);

        return ['changed' =>
            ['type' => 'sheet',
                'key' => $key,
                'oldValue' => $decodedFirstFile[$key],
                'newValue' => $decodedSecondFile[$key]]];
    }, array: $keys);
    //print_r($result);
    return $result;
}
