<?php

namespace Differ\Formatters\Stylish;

function getStylishOutput(mixed $fileAST): string
{
//    print_r($fileAST);
    $iter = static function (array $node, int $depth) use (&$iter) {
        $mapped = array_map(static function ($value) use ($iter, $depth) {
            $spaceUnchanged = str_repeat('  ', $depth);
            $spaceChanged = str_repeat('  ', $depth - 1);

            $value1 = $value['value1'] ?? null;
            $value2 = $value['value2'] ?? null;

            if ($value['status'] === 'unchanged' || $value['status'] === 'nested') {
                if (is_array($value1)) {
                    return "$spaceUnchanged$spaceUnchanged{$value['key']}: {$iter($value1, $depth + 1)}";
                }
                $valueString = toString($value1);
                return "$spaceUnchanged$spaceUnchanged{$value['key']}: $valueString";
            }

            if ($value['status'] === 'deleted') {
                if (is_array($value1)) {
                    return "$spaceUnchanged$spaceChanged- {$value['key']}: {$iter($value1, $depth + 1)}";
                }
                $oldValue = toString($value1);
                return "$spaceUnchanged$spaceChanged- {$value['key']}: $oldValue";
            }

            if ($value['status'] === 'added') {
                if (is_array($value2)) {
                    return "$spaceUnchanged$spaceChanged+ {$value['key']}: {$iter($value2, $depth + 1)}";
                }
                $newValue = toString($value2);
                return "$spaceUnchanged$spaceChanged+ {$value['key']}: $newValue";
            }

            if ($value['status'] === 'changed') {
//                if (isset($value['oldType'])) {
                if (is_array($value1) && !is_array($value2)) {
                    $newValue = toString($value2);
                    return $spaceUnchanged . $spaceChanged . "- " . $value['key'] . ": " .
                        $iter($value1, $depth + 1) . "\n" . $spaceUnchanged . $spaceChanged . "+ "
                        . $value['key'] . ": " . $newValue;
                }

                if (!is_array($value1) && is_array($value2)) {
                    $oldValue = toString($value['value1']);
                    return $spaceUnchanged . $spaceChanged . "- " . $value['key'] . ": " . $oldValue . "\n" .
                        $spaceUnchanged . $spaceChanged . "+ " . $value['key'] . ": " .
                        $iter($value2, $depth + 1);
                }
//                }
                if (is_array($value1)) {
                    return "$spaceUnchanged$spaceUnchanged{$value['key']}: {$iter($value1, $depth + 1)}";
                }
            }

            $oldValue = toString($value1);
            $newValue = toString($value2);

            return $spaceUnchanged . $spaceChanged . "- " . $value['key'] . ": " . $oldValue . "\n" .
                $spaceUnchanged . $spaceChanged . "+ " . $value['key'] . ": " . $newValue;
        }, $node);

        $string = implode("\n", $mapped);
        $bracketIndent = str_repeat('  ', ($depth - 1) * 2);
        return '{' . "\n" . $string . "\n" . $bracketIndent . '}';
    };

    return $iter($fileAST, 1);
}

function toString(mixed $value): string
{
    if (is_string($value)) {
        return $value;
    }
    return strtolower(trim(var_export($value, true), "'"));
}
