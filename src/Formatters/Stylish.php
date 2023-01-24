<?php

namespace Differ\Formatters\Stylish;

function getOutput(mixed $fileAST): string
{
    $iter = static function (array $node, int $depth) use (&$iter) {
        $mapped = array_map(static function ($value) use ($iter, $depth) {
            $spaceUnchanged = str_repeat('  ', $depth);
            $spaceChanged = str_repeat('  ', $depth - 1);
            
            if ($value['status'] === 'unchanged') {
                if ($value['type'] === 'node') {
                    return "$spaceUnchanged$spaceUnchanged{$value['key']}: {$iter($value['children'], $depth + 1)}";
                }
                $valueString = toString($value['value'], $depth);
                return "$spaceUnchanged$spaceUnchanged{$value['key']}: $valueString";
            }

            if ($value['status'] === 'nested') {
//                if ($value['type'] === 'node') {
//                    return "$spaceUnchanged$spaceUnchanged{$value['key']}: {$iter($value['children'], $depth + 1)}";
//                }
                $valueString = toString($value['value'], $depth);
                return "$spaceUnchanged$spaceUnchanged{$value['key']}: $valueString";
            }

            if ($value['status'] === 'deleted') {
//                if ($value['type'] === 'node') {
//                    return "$spaceUnchanged$spaceChanged- {$value['oldKey']}: {$iter($value['children'], $depth + 1)}";
//                }
                $oldValue = toString($value['oldValue'], $depth);
                return "$spaceUnchanged$spaceChanged- {$value['oldKey']}: $oldValue";
            }

            if ($value['status'] === 'added') {
//                if ($value['type'] === 'node') {
//                    return "$spaceUnchanged$spaceChanged+ {$value['newKey']}: {$iter($value['children'], $depth + 1)}";
//                }
                $newValue = toString($value['newValue'], $depth);
                return "$spaceUnchanged$spaceChanged+ {$value['newKey']}: $newValue";
            }

            if ($value['status'] === 'changed') {
                if (isset($value['oldType'])) {
                    $oldValue = toString($value['oldValue'], $depth);
                    $newValue = toString($value['newValue'], $depth);

                    if ($value['oldType'] === 'node' && $value['newType'] === 'sheet') {
                        return $spaceUnchanged . $spaceChanged . "- " . $value['key'] . ": " .
                            $oldValue . "\n" . $spaceUnchanged . $spaceChanged . "+ "
                            . $value['key'] . ": " . $newValue;
                    }

                    if ($value['oldType'] === 'sheet' && $value['newType'] === 'node') {
                        return $spaceUnchanged . $spaceChanged . "- " . $value['key'] . ": " . $oldValue . "\n" .
                            $spaceUnchanged . $spaceChanged . "+ " . $value['key'] . ": " . $newValue;
                    }
                }

                if ($value['type'] === 'node') {
                    return "$spaceUnchanged$spaceUnchanged{$value['key']}: {$iter($value['children'], $depth + 1)}";
                }
            }

            $oldValue = toString($value['oldValue'], $depth);
            $newValue = toString($value['newValue'], $depth);

            return $spaceUnchanged . $spaceChanged . "- " . $value['key'] . ": " . $oldValue . "\n" .
                $spaceUnchanged . $spaceChanged . "+ " . $value['key'] . ": " . $newValue;
        }, $node);

        $string = implode("\n", $mapped);
        $bracketIndent = str_repeat('  ', ($depth - 1) * 2);
        return '{' . "\n" . $string . "\n" . $bracketIndent . '}';
    };

    return $iter($fileAST, 1);
}

function toString(mixed $value, int $depth = 1): string
{
    if (is_string($value)) {
        return $value;
    }

    if (is_array($value)) {
        return stringify($value, $depth);
    }

    return strtolower(trim(var_export($value, true), "'"));
}

function stringify($node, $depth = 1): string
{
    if (!is_array($node)) {
        return toString($node);
    }
    $replacer = '  ';

    $children = array_map(function ($child, $key) use ($replacer, $depth) {
        $indent = str_repeat($replacer, $depth);
        if (is_array($child)) {
            $iteration = stringify($child, $depth + 1);
            return "{$indent}{$key}: {$iteration}";
        }
        $value = toString($child);
        return "{$indent}{$key}: {$value}";
    }, $node, array_keys($node));

    $arrayToString = implode("\n", $children);
    $bracketIndent = str_repeat($replacer, $depth - 1);
    return "{\n{$arrayToString}\n{$bracketIndent}}";
}
