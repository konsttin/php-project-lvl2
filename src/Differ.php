<?php

namespace Hexlet\Code\Differ;

function genDiff($firstFile, $secondFile)
{
    $file1 = json_decode($firstFile, true, 512, JSON_THROW_ON_ERROR);
    $file2 = json_decode($secondFile, true, 512, JSON_THROW_ON_ERROR);

    $result = [];

    foreach ($file1 as $key1 => $value1) {
        foreach($file2 as $key2 => $value2) {
            if (!array_key_exists($key1, $file2)) {
                $result[] = '- ' . $key1 . ': ' . $value1;
            }
            if (!array_key_exists($key2, $file1)) {
                $result[] = '+ ' . $key2 . ': ' . $value2;
            }
            if ($key1 === $key2 && $value1 === $value2) {
                $result[] = '  ' . $key1 . ': ' . $value1;
            }
            if ($key1 === $key2 && $value1 !== $value2) {
                $result[] = '- ' . $key1 . ': ' . $value1 . "\n " . '+ ' . $key2 . ': ' . $value2;
            }
        }
    }
    return $result;
}