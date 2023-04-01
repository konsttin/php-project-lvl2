<?php

namespace Differ\Formatters\Stylish;

/**
 * @param array<mixed> $fileAST
 * @param int $depth
 * @return string
 * @throws \Exception
 */
function getStylishOutput(mixed $fileAST, int $depth = 0): string
{
    $indent = str_repeat('    ', $depth);
//    print_r($fileAST);

    $lines = array_map(static function ($node) use ($indent, $depth) {

        $node = getNestedNode($node);

        ['status' => $status, 'key' => $key, 'value1' => $value, 'value2' => $value2] = $node;

        $normalizeValue1 = is_array($value) ? getStylishOutput($value, $depth + 1) : toString($value);

        switch ($status) {
            case 'nested':
            case 'unchanged':
                return "$indent    $key: $normalizeValue1";
            case 'added':
                return "$indent  + $key: $normalizeValue1";
            case 'deleted':
                return "$indent  - $key: $normalizeValue1";
            case 'changed':
                $normalizeValue2 = is_array($value2) ? getStylishOutput($value2, $depth + 1) : toString($value2);
                return "$indent  - $key: $normalizeValue1\n$indent  + $key: $normalizeValue2";
            default:
                throw new \Exception("Unknown node status: {$node['status']}");
        }
    }, $fileAST);
    $result = ["{", ...$lines, "$indent}"];
    return implode("\n", $result);
}

/**
 * @throws \Exception
 */
function toString(mixed $value, int $depth = 0): string
{
    if (is_string($value)) {
        return $value;
    }

    return strtolower(trim(var_export($value, true), "'"));
}

/**
 * @param mixed $content
 * @return mixed
 */
function getNestedNode(mixed $content): mixed
{
    $iter = static function ($content) use (&$iter) {

        if (!is_array($content)) {
            return $content;
        }

        if (array_key_exists('status', $content)) {
            return $content;
        }

        $keys = array_keys($content);
        return array_map(static function ($key) use ($content, $iter) {
            $value = is_array($content[$key]) ? $iter($content[$key]) : $content[$key];
            return ['status' => 'unchanged',
                'key' => $key,
                'value1' => $value,
                'value2' => null];
        }, $keys);
    };

    return $iter($content);
}

/**
 * @param mixed $value
 * @param int $spacesCount
 * @return string
 * @throws \Exception
 */
function stringify(mixed $value, int $spacesCount = 1): string
{
    if (isset($value['status'])) {
        return $value;
    }

    $iter = function ($currentValue, $depth) use (&$iter, $spacesCount) {
        $replacer = '    ';

        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        $indentSize = $depth * $spacesCount;
        $currentIndent = str_repeat($replacer, $indentSize);
        $bracketIndent = str_repeat($replacer, $indentSize - $spacesCount);

        $lines = array_map(
            fn($key, $val) => "{$currentIndent}{$key}: {$iter($val, $depth + 1)}",
            array_keys($currentValue),
            $currentValue
        );

        $result = ['{', ...$lines, "{$bracketIndent}}"];

        return implode("\n", $result);
    };

    return $iter($value, 1);
}
