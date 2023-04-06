<?php

namespace Differ\Formatters\Stylish;

use Exception;

/**
 * @param array<mixed> $fileAST
 * @param int $depth
 * @return string
 * @throws Exception
 */
function getStylishOutput(mixed $fileAST, int $depth = 0): string
{
//    print_r($fileAST);

    $indent = str_repeat('    ', $depth);

    $lines = array_map(static function ($node) use ($indent, $depth) {

        ['status' => $status, 'key' => $key, 'value1' => $value, 'value2' => $value2] = $node;

        $normalizeValue1 = is_array($value) ? stringify($value, $depth + 1) : toString($value);

        switch ($status) {
            case 'nested':
            case 'unchanged':
                $normalizeValue1 = is_array($value) ? getStylishOutput($value, $depth + 1) : toString($value);
                return "$indent    $key: $normalizeValue1";
            case 'added':
                return "$indent  + $key: $normalizeValue1";
            case 'deleted':
                return "$indent  - $key: $normalizeValue1";
            case 'changed':
                $normalizeValue2 = is_array($value2) ? getStylishOutput($value2, $depth + 1) : toString($value2);
                return "$indent  - $key: $normalizeValue1\n$indent  + $key: $normalizeValue2";
            default:
                $nodePrint = json_encode($node, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
                throw new Exception("Unknown node status: $nodePrint");
        }
    }, $fileAST);
    $result = ["{", ...$lines, "$indent}"];
    return implode("\n", $result);
}

/**
 * @param mixed $value
 * @param int $spacesCount
 * @return mixed
 */
function stringify(mixed $value, int $spacesCount = 1): mixed
{
    if (array_key_exists('status', $value)) {
        return $value;
    }

    $iter = static function ($currentValue, $depth) use (&$iter, $spacesCount) {

        $replacer = '    ';

        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        $indentSize = $depth * $spacesCount;
        $indent = str_repeat($replacer, $indentSize);

        $lines = array_map(
            static fn($key, $val) => "$indent    $key: {$iter($val, $depth + 1)}",
            array_keys($currentValue),
            $currentValue
        );

        $result = ['{', ...$lines, "$indent}"];

        return implode("\n", $result);
    };

    return $iter($value, 1);
}

/**
 * @param mixed $value
 * @return string
 */
function toString(mixed $value): string
{
    if (is_string($value)) {
        return $value;
    }

    return strtolower(trim(var_export($value, true), "'"));
}
