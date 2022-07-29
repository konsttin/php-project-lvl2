<?php

namespace Hexlet\Code;

use Exception;

use function src\Parser\parser;

function fileDecode($filePath): mixed
{
    $path = __DIR__ . '/../' . $filePath;

    $extension = pathinfo($path, PATHINFO_EXTENSION);

    $contentOfFile = file_get_contents($path);
    if ($contentOfFile === false) {
        throw new Exception();
    }

    switch ($extension) {
        case 'json':
            $decodedFile = json_decode($contentOfFile, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);
            break;
        case 'yml' || 'yaml':
            $decodedFile = yaml_parse_file($contentOfFile);
            break;
        default:
            throw new Exception('Unexpected extension');
    }
    return $decodedFile;
}

function genDiff(string $firstFile, string $secondFile): string
{
//    $contentOfFirstFile = file_get_contents(__DIR__ . '/../' . $firstFile);
//    if ($contentOfFirstFile === false) {
//        throw new Exception();
//    }
//
//    $contentOfSecondFile = file_get_contents(__DIR__ . '/../' . $secondFile);
//    if ($contentOfSecondFile === false) {
//        throw new Exception();
//    }
//
//    $decodedFirstFile = json_decode($contentOfFirstFile, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);
//    $decodedSecondFile = json_decode($contentOfSecondFile, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);
    $decodedFirstFile = fileDecode($firstFile);
    $decodedSecondFile = fileDecode($secondFile);

    return parser($decodedFirstFile, $decodedSecondFile);
}
