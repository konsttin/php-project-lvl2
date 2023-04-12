<?php

namespace Differ\Formatters\Plain;

/**
 * @param mixed $fileAST
 * @return string
 * @throws \Exception
 */
function plain(mixed $fileAST): string
{
    $iter = static function (array $node, string $previousKeys = '') use (&$iter) {
        $mapped = array_map(static function ($value) use ($iter, $previousKeys) {
            ['status' => $status, 'key' => $key, 'value1' => $value1, 'value2' => $value2] = $value;

            $currentKeyPath = $previousKeys === '' ? $key : "$previousKeys.$key";

            switch ($status) {
                case 'nested':
                    return $iter($value1, $currentKeyPath);
                case 'added':
                    $normalizeValue = getNormalizedValue($value1);
                    return "Property '$currentKeyPath' was added with value: $normalizeValue";
                case 'deleted':
                    return "Property '$currentKeyPath' was removed";
                case 'changed':
                    $normalizeValue = getNormalizedValue($value1);
                    $normalizeValue2 = getNormalizedValue($value2);
                    return "Property '$currentKeyPath' was updated. From $normalizeValue to $normalizeValue2";
                case 'unchanged':
                    break;
                default:
                    throw new \Exception("Unknown node status: {$status}");
            }
            return null;
        }, $node);

        $filtered = array_filter($mapped);
        return implode("\n", $filtered);
    };

    return $iter($fileAST);
}

/**
 * @param mixed $value
 * @return string
 */
function getNormalizedValue(mixed $value): string
{
    if (is_array($value)) {
        return "[complex value]";
    }

    if (is_string($value)) {
        return "'$value'";
    }

    if ($value === 'null') {
        return $value;
    }

    return trim(var_export($value, true), "'");
}
