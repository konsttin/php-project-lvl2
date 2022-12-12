<?php

namespace Differ\Formatters\Plain;

function getOutput(mixed $fileAST): string
{
    $iter = static function (array $node, string $previousKeys = '') use (&$iter) {
        $mapped = array_map(static function ($value) use ($iter, $previousKeys) {
            if ($value['status'] === 'deleted') {
                if ($previousKeys === '') {
                    return "Property '{$value['oldKey']}' was removed";
                }
                $currentPath = "$previousKeys.{$value['oldKey']}";
                return "Property '$currentPath' was removed";
            }

            if ($value['status'] === 'added') {
                if ($value['type'] === 'node') {
                    if ($previousKeys === '') {
                        return "Property '{$value['newKey']}' was added with value: [complex value]";
                    }
                    $currentPath = "$previousKeys.{$value['newKey']}";
                    return "Property '$currentPath' was added with value: [complex value]";
                }
                $newValue = toStringWithQuotes($value['newValue']);

                if ($previousKeys === '') {
                    return "Property '{$value['newKey']}' was added with value: $newValue";
                }
                $currentPath = "$previousKeys.{$value['newKey']}";
                return "Property '$currentPath' was added with value: $newValue";
            }

            if ($value['status'] === 'changed') {
                if (isset($value['oldType'])) {
                    if ($value['oldType'] === 'node' && $value['newType'] === 'sheet') {
                        $newValue = toStringWithQuotes($value['newValue']);

                        if ($previousKeys === '') {
                            return "Property '{$value['key']}' was updated. 
                            From [complex value] to $newValue";
                        }
                        $currentPath = "$previousKeys.{$value['key']}";
                        return "Property '$currentPath' was updated. From [complex value] to $newValue";
                    }

                    if ($value['oldType'] === 'sheet' && $value['newType'] === 'node') {
                        $oldValue = toStringWithQuotes($value['oldValue']);

                        if ($previousKeys === '') {
                            return "Property '{$value['key']}' was updated. 
                            From $oldValue to [complex value]";
                        }
                        $currentPath = "$previousKeys.{$value['key']}";
                        return "Property '$currentPath' was updated. From $oldValue to [complex value]";
                    }
                }

                if ($value['type'] === 'node') {
                    if ($previousKeys === '') {
                        return $iter($value['children'], $value['key']);
                    }
                    $currentPath = "$previousKeys.{$value['key']}";
                    return $iter($value['children'], $currentPath);
                }

                $oldValue = toStringWithQuotes($value['oldValue']);
                $newValue = toStringWithQuotes($value['newValue']);

                if ($previousKeys === '') {
                    return "Property '{$value['key']}' was updated. 
                    From $oldValue to $newValue";
                }
                $currentPath = "$previousKeys.{$value['key']}";
                return "Property '$currentPath' was updated. From $oldValue to $newValue";
            }

            return null;
        }, $node);

        $filtered = array_filter($mapped);
        return implode("\n", $filtered);
    };

    return $iter($fileAST);
}

function toStringWithQuotes(mixed $value): string
{
    if (is_string($value)) {
        return "'$value'";
    }
    return strtolower(trim(var_export($value, true), "'"));
}
