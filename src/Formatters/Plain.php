<?php

namespace Differ\Formatters\Plain;

/**
 * @param mixed $fileAST
 * @return string
 * @throws \Exception
 */
function getPlainOutput(mixed $fileAST): string
{
    $iter = static function (array $node, string $previousKeys = '') use (&$iter) {
        $mapped = array_map(static function ($value) use ($iter, $previousKeys) {

            $currentKeyPath = $previousKeys === '' ? $value['key'] : "$previousKeys.{$value['key']}";

            switch ($value['status']) {
                case 'nested':
                    return $iter($value['value1'], $currentKeyPath);
                case 'added':
                    $normalizeValue = getNormalizeValue($value['value1']);
                    return "Property '$currentKeyPath' was added with value: $normalizeValue";
                case 'deleted':
                    return "Property '$currentKeyPath' was removed";
                case 'changed':
                    $normalizeValue = getNormalizeValue($value['value1']);
                    $normalizeValue2 = getNormalizeValue($value['value2']);
                    return "Property '$currentKeyPath' was updated. From $normalizeValue to $normalizeValue2";
                case 'unchanged':
                    break;
                default:
                    throw new \Exception("Unknown node status: {$value['status']}");
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
function getNormalizeValue(mixed $value): string
{
    if (is_array($value)) {
        return "[complex value]";
    }

    if (is_string($value)) {
        return "'$value'";
    }

    return strtolower(trim(var_export($value, true), "'"));
}
