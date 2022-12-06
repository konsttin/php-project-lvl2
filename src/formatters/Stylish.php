<?php

namespace Differ\Formatters\Stylish;

function getStylishOutput(mixed $fileAST): string
{
    $iter = static function (array $node, int $depth) use (&$iter) {
        $mapped = array_map(static function ($value) use ($iter, $depth) {
            $indent = str_repeat('  ', $depth);
            $indent2 = str_repeat('  ', $depth - 1);

            if ($value['status'] === 'unchanged' || $value['status'] === 'nested') {
                if ($value['type'] === 'node') {
                    return "$indent$indent{$value['key']}: {$iter($value['children'], $depth + 1)}";
                }
                $valueString = toString($value['value']);
                return "$indent$indent{$value['key']}: $valueString";
            }

            if ($value['status'] === 'deleted') {
                if ($value['type'] === 'node') {
                    return "$indent$indent2- {$value['oldKey']}: {$iter($value['children'], $depth + 1)}";
                }
                $oldValue = toString($value['oldValue']);
                return "$indent$indent2- {$value['oldKey']}: $oldValue";
            }

            if ($value['status'] === 'added') {
                if ($value['type'] === 'node') {
                    return "$indent$indent2+ {$value['newKey']}: {$iter($value['children'], $depth + 1)}";
                }
                $newValue = toString($value['newValue']);
                return "$indent$indent2+ {$value['newKey']}: $newValue";
            }

            if ($value['status'] === 'changed') {
                if (isset($value['oldType'])) {
                    if ($value['oldType'] === 'node' && $value['newType'] === 'sheet') {
                        $newValue = toString($value['newValue']);
                        return $indent . $indent2 . "- " . $value['key'] . ": " .
                            $iter($value['oldChildren'], $depth + 1) . "\n" . $indent . $indent2 . "+ "
                            . $value['key'] . ": " . $newValue;
                    }

                    if ($value['oldType'] === 'sheet' && $value['newType'] === 'node') {
                        $oldValue = toString($value['oldValue']);
                        return $indent . $indent2 . "- " . $value['key'] . ": " . $oldValue . "\n" .
                            $indent . $indent2 . "+ " . $value['key'] . ": " . $iter($value['newChildren'], $depth + 1);
                    }
                }

                if ($value['type'] === 'node') {
                    return "$indent$indent{$value['key']}: {$iter($value['children'], $depth + 1)}";
                }
            }

            $oldValue = toString($value['oldValue']);
            $newValue = toString($value['newValue']);

            return $indent . $indent2 . "- " . $value['key'] . ": " . $oldValue . "\n" .
                $indent . $indent2 . "+ " . $value['key'] . ": " . $newValue;
        }, $node);

        $string = implode("\n", $mapped);
        $bracketIndent = str_repeat('  ', ($depth - 1) * 2);
        return '{' . "\n" . $string . "\n" . $bracketIndent . '}';
    };

    $result = $iter($fileAST, 1);
    echo($result);
    return $result;
}

function toString(mixed $value): string
{
    if (is_string($value)) {
        return $value;
    }
    return strtolower(trim(var_export($value, true), "'"));
}
