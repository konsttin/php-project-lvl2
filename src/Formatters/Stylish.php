<?php

namespace Differ\Formatters\Stylish;

function getOutput(mixed $fileAST): string
{
    $iter = static function (array $node, int $depth) use (&$iter) {
        $mapped = array_map(static function ($value) use ($iter, $depth) {
            $spaceUnchanged = str_repeat('  ', $depth);
            $spaceChanged = str_repeat('  ', $depth - 1);

            $normalizeValue1 = is_array($value) ? $iter($value, $depth + 1) : $value;

            if ($value['status'] === 'unchanged') {
                return "$spaceUnchanged$spaceUnchanged{$value['key']}: {$normalizeValue1}";
            }

            if ($value['status'] === 'nested') {
                return "$spaceUnchanged$spaceUnchanged{$value['key']}: {$normalizeValue1}";
            }

            if ($value['status'] === 'deleted') {
                return "$spaceUnchanged$spaceChanged- {$value['key']}: {$normalizeValue1}";
            }

            if ($value['status'] === 'added') {
                $normalizeValue2 = is_array($value['value2']) ? $iter($value['value2'], $depth + 1) : $value['value2'];
                return "$spaceUnchanged$spaceChanged+ {$value['key']}: {$normalizeValue2}";
            }

            if ($value['status'] === 'changed') {
                if (isset($value['children'])) {
                        return "$spaceUnchanged$spaceUnchanged{$value['key']}: {$iter($value['children'], $depth + 1)}";
                    }
                }


            $oldValue = $value['value1'];
            $newValue = $value['value2'];

            return $spaceUnchanged . $spaceChanged . "- " . $value['key'] . ": " . $oldValue . "\n" .
                $spaceUnchanged . $spaceChanged . "+ " . $value['key'] . ": " . $newValue;
        }, $node);

        $string = implode("\n", $mapped);
        $bracketIndent = str_repeat('  ', ($depth - 1) * 2);
        return '{' . "\n" . $string . "\n" . $bracketIndent . '}';
    };

    return $iter($fileAST, 1);
}

//function toString(mixed $value, int $depth = 1): string
//{
//    if (is_string($value)) {
//        return $value;
//    }
//
//    if (is_array($value)) {
//        return stringify($value, $depth);
//    }
//
//    return strtolower(trim(var_export($value, true), "'"));
//}

//function stringify(mixed $node, int $depth = 1): string
//{
//    if (!is_array($node)) {
//        return toString($node);
//    }
//    $replacer = '  ';
//
//    $children = array_map(function ($child, $key) use ($replacer, $depth) {
//        $indent = str_repeat($replacer, $depth);
//        if (is_array($child)) {
//            $iteration = stringify($child, $depth + 1);
//            return "{$indent}{$key}: {$iteration}";
//        }
//        $value = toString($child);
//        return "{$indent}{$key}: {$value}";
//    }, $node, array_keys($node));
//
//    $arrayToString = implode("\n", $children);
//    $bracketIndent = str_repeat($replacer, $depth - 1);
//    return "{\n{$arrayToString}\n{$bracketIndent}}";
//}
