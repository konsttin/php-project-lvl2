<?php

namespace Hexlet\Code\Differ;

function genDiff($firstFile, $secondFile)
{
    $file1 = json_decode($firstFile, true, 512, JSON_THROW_ON_ERROR);
    $file2 = json_decode($secondFile, true, 512, JSON_THROW_ON_ERROR);

    $result = array_reduce($file1, function ($acc, $value1) use ($file1, $file2) {
        foreach ($file2 as $key2 => $value2) {
            if (!array_key_exists(key($value1), $file2)) {
                $acc = '- ' . $value1;
            }
            if (!array_key_exists($key2, $file1)) {
                $acc = '+ ' . $value2;
            }
            if (key($value1) === $key2 && $value1 === $value2) {
                $acc = $value1;
            }
            if (key($value1) === $key2 && $value1 !== $value2) {
                $acc = '- ' . $value1 . "\n" . '+ ' . $file1[$key2];
            }
        }
        return $acc;
    }, '');
    print_r($result);
    return $result;
}