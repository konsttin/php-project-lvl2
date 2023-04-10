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
    $indent = str_repeat('    ', $depth);

    $lines = array_map(static function ($node) use ($indent, $depth) {

        ['status' => $status, 'key' => $key, 'value1' => $value, 'value2' => $value2] = $node;

        $stringifyValue1 = is_array($value) ? stringify($value, $depth + 1) : toString($value);

        switch ($status) {
            case 'nested':
                $nestedValue1 = is_array($value) ? getStylishOutput($value, $depth + 1) : toString($value);
                return "$indent    $key: $nestedValue1";
            case 'unchanged':
                return "$indent    $key: $stringifyValue1";
            case 'added':
                return "$indent  + $key: $stringifyValue1";
            case 'deleted':
                return "$indent  - $key: $stringifyValue1";
            case 'changed':
                $stringifyValue2 = is_array($value2) ? stringify($value2, $depth + 1) : toString($value2);
                return "$indent  - $key: $stringifyValue1\n$indent  + $key: $stringifyValue2";
            default:
                throw new Exception("Unknown node status: $status");
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
