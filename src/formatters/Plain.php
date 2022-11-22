<?php

namespace src\formatters\Plain;

function plain(mixed $fileAST): string
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
                $value['newValue'] = toStringWithQuotes($value['newValue']);

                if ($previousKeys === '') {
                    return "Property '{$value['newKey']}' was added with value: {$value['newValue']}";
                }
                $currentPath = "$previousKeys.{$value['newKey']}";
                return "Property '$currentPath' was added with value: {$value['newValue']}";
            }

            if ($value['status'] === 'changed') {
                if (!empty($value['oldType'])) {
                    if ($value['oldType'] === 'node' && $value['newType'] === 'sheet') {
                        $value['newValue'] = toStringWithQuotes($value['newValue']);

                        if ($previousKeys === '') {
                            return "Property '{$value['key']}' was updated. 
                            From [complex value] to {$value['newValue']}";
                        }
                        $currentPath = "$previousKeys.{$value['key']}";
                        return "Property '$currentPath' was updated. From [complex value] to {$value['newValue']}";
                    }

                    if ($value['oldType'] === 'sheet' && $value['newType'] === 'node') {
                        $value['oldValue'] = toStringWithQuotes($value['oldValue']);

                        if ($previousKeys === '') {
                            return "Property '{$value['key']}' was updated. 
                            From {$value['oldValue']} to [complex value]";
                        }
                        $currentPath = "$previousKeys.{$value['key']}";
                        return "Property '$currentPath' was updated. From {$value['oldValue']} to [complex value]";
                    }
                }

                if ($value['type'] === 'node') {
                    if ($previousKeys === '') {
                        return $iter($value['children'], $value['key']);
                    }
                    $currentPath = "$previousKeys.{$value['key']}";
                    return $iter($value['children'], $currentPath);
                }

                $value['oldValue'] = toStringWithQuotes($value['oldValue']);
                $value['newValue'] = toStringWithQuotes($value['newValue']);

                if ($previousKeys === '') {
                    return "Property '{$value['key']}' was updated. 
                    From {$value['oldValue']} to {$value['newValue']}";
                }
                $currentPath = "$previousKeys.{$value['key']}";
                return "Property '$currentPath' was updated. From {$value['oldValue']} to {$value['newValue']}";
            }

            return null;
        }, $node);

        $mapped = array_filter($mapped);
        return implode("\n", $mapped);
    };

    $result = $iter($fileAST);
    print_r($result);
    return $result;
}

function toStringWithQuotes(mixed $value): string
{
    if (is_string($value)) {
        return "'{$value}'";
    }
    return strtolower(trim(var_export($value, true), "'"));
}
