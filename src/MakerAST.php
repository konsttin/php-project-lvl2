<?php

namespace src\MakerAST;

function makeAST(mixed $decodedFirstFile, mixed $decodedSecondFile = false): mixed
{
    $merge = !is_array($decodedSecondFile) ? $decodedFirstFile : array_merge($decodedFirstFile, $decodedSecondFile);
    $keys = array_keys($merge);
    sort($keys);

    return array_map(callback: static function ($key) use ($decodedFirstFile, $decodedSecondFile) {
        if ($decodedSecondFile === false) {
            if (is_array($decodedFirstFile[$key])) {
                return ['status' => 'nested',
                    'type' => 'node',
                    'key' => $key,
                    'children' => makeAST($decodedFirstFile[$key])];
            }

            return ['status' => 'nested',
                'type' => 'sheet',
                'key' => $key,
                'value' => $decodedFirstFile[$key]];
        }

        if (!array_key_exists($key, $decodedSecondFile)) {
            if (is_array($decodedFirstFile[$key])) {
                return ['status' => 'deleted',
                    'type' => 'node',
                    'oldKey' => $key,
                    'children' => makeAST($decodedFirstFile[$key])];
            }

            return ['status' => 'deleted',
                'type' => 'sheet',
                'oldKey' => $key,
                'oldValue' => $decodedFirstFile[$key]];
        }

        if (!array_key_exists($key, $decodedFirstFile)) {
            if (is_array($decodedSecondFile[$key])) {
                return ['status' => 'added',
                    'type' => 'node',
                    'newKey' => $key,
                    'children' => makeAST($decodedSecondFile[$key])];
            }

            return ['status' => 'added',
                'type' => 'sheet',
                'newKey' => $key,
                'newValue' => $decodedSecondFile[$key]];
        }

        if ($decodedFirstFile[$key] === $decodedSecondFile[$key]) {
            if (is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
                return ['status' => 'unchanged',
                    'type' => 'node',
                    'key' => $key,
                    'children' => makeAST($decodedFirstFile[$key], $decodedSecondFile[$key])];
            }

            return ['status' => 'unchanged',
                'type' => 'sheet',
                'key' => $key,
                'value' => $decodedFirstFile[$key]];
        }

        if (is_array($decodedFirstFile[$key]) && !is_array($decodedSecondFile[$key])) {
            return ['status' => 'changed',
                'oldType' => 'node',
                'newType' => 'sheet',
                'key' => $key,
                'oldChildren' => makeAST($decodedFirstFile[$key]),
                'newValue' => $decodedSecondFile[$key]];
        }

        if (!is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
            return ['status' => 'changed',
                'oldType' => 'sheet',
                'newType' => 'node',
                'key' => $key,
                'oldValue' => $decodedFirstFile[$key],
                'newChildren' => makeAST($decodedSecondFile[$key])];
        }

        if (is_array($decodedFirstFile[$key]) && is_array($decodedSecondFile[$key])) {
            return ['status' => 'changed',
                'type' => 'node',
                'key' => $key,
                'children' => makeAST($decodedFirstFile[$key], $decodedSecondFile[$key])];
        }

        return ['status' => 'changed',
            'type' => 'sheet',
            'key' => $key,
            'oldValue' => $decodedFirstFile[$key],
            'newValue' => $decodedSecondFile[$key]];
    }, array: $keys);
}

//function toString(mixed $value): string
//{
//    return strtolower(trim(var_export($value, true), "'"));
//}
