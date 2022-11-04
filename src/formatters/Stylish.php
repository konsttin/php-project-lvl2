<?php

namespace src\formatters\Stylish;

function stylish(mixed $fileAST): string
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
                    return $indent . $indent2 . "- " . $value['key'] . ": " .
                        $iter($value['oldChildren'], $depth + 1) . "\n" . $indent . $indent2 . "+ "
                        . $value['key'] . ": " . $value['newValue'];
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

        $string = implode("\n", $mapped);
        $bracketIndent = str_repeat('  ', ($depth - 1) * 2);
        return '{' . "\n" . $string . "\n" . $bracketIndent . '}';
    };

    $result = $iter($fileAST, 1);
    print_r($result);
    return $result;
}
