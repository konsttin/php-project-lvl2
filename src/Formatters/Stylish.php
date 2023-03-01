<?php

namespace Differ\Formatters\Stylish;

function getOutput(mixed $fileAST): string
{
//    var_dump($fileAST);
    $iter = static function (array $node, int $depth) use (&$iter) {
        $mapped = array_map(static function ($value) use ($iter, $depth) {
            $spaceUnchanged = str_repeat('  ', $depth);
            $spaceChanged = str_repeat('  ', $depth - 1);

            if ($value['status'] === 'unchanged' || $value['status'] === 'nested') {
                if ($value['type'] === 'node') {
                    return "$spaceUnchanged$spaceUnchanged{$value['key']}: {$iter($value['children'], $depth + 1)}";
                }
                $valueString = toString($value['value']);
                return "$spaceUnchanged$spaceUnchanged{$value['key']}: $valueString";
            }

            if ($value['status'] === 'deleted') {
                if ($value['type'] === 'node') {
                    return "$spaceUnchanged$spaceChanged- {$value['oldKey']}: {$iter($value['children'], $depth + 1)}";
                }
                $oldValue = toString($value['oldValue']);
                return "$spaceUnchanged$spaceChanged- {$value['oldKey']}: $oldValue";
            }

            if ($value['status'] === 'added') {
                if ($value['type'] === 'node') {
                    return "$spaceUnchanged$spaceChanged+ {$value['newKey']}: {$iter($value['children'], $depth + 1)}";
                }
                $newValue = toString($value['newValue']);
                return "$spaceUnchanged$spaceChanged+ {$value['newKey']}: $newValue";
            }

            if ($value['status'] === 'changed') {
                if (isset($value['oldType'])) {
                    if ($value['oldType'] === 'node' && $value['newType'] === 'sheet') {
                        $newValue = toString($value['newValue']);
                        return $spaceUnchanged . $spaceChanged . "- " . $value['key'] . ": " .
                            $iter($value['oldChildren'], $depth + 1) . "\n" . $spaceUnchanged . $spaceChanged . "+ "
                            . $value['key'] . ": " . $newValue;
                    }

                    if ($value['oldType'] === 'sheet' && $value['newType'] === 'node') {
                        $oldValue = toString($value['oldValue']);
                        return $spaceUnchanged . $spaceChanged . "- " . $value['key'] . ": " . $oldValue . "\n" .
                            $spaceUnchanged . $spaceChanged . "+ " . $value['key'] . ": " .
                            $iter($value['newChildren'], $depth + 1);
                    }
                }

                if ($value['type'] === 'node') {
                    return "$spaceUnchanged$spaceUnchanged{$value['key']}: {$iter($value['children'], $depth + 1)}";
                }
            }

            $oldValue = toString($value['oldValue']);
            $newValue = toString($value['newValue']);

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

//function stringify($content)
//{
//    $iter = function ($content) use (&$iter) {
//        if (!is_array($content)) {
//            if ($content === null) {
//                return 'null';
//            }
//            return trim(var_export($content, true), "'");
//        }
//
//        $keys = array_keys($content);
//        return array_map(function ($key) use ($content, $iter) {
//            $value = (is_array($content[$key])) ? $iter($content[$key]) : $content[$key];
//
//            return makeNode('unchanged', $key, $value);
//        }, $keys);
//    };
//
//    return $iter($content);
//}
