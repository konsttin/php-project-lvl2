<?php

namespace Hexlet\Code;

use Exception;
use Symfony\Component\Yaml\Yaml;

use function src\formatters\Stylish\stylish;
use function src\Formatter\format;

function fileDecode(string $filePath): mixed
{
    $path = __DIR__ . '/../' . $filePath;

    $extension = pathinfo($path, PATHINFO_EXTENSION);

    $contentOfFile = file_get_contents($path);
    if ($contentOfFile === false) {
        throw new Exception('File is empty');
    }

    switch ($extension) {
        case 'json':
            $decodedFile = json_decode($contentOfFile, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);
            break;
        case 'yaml':
        case 'yml':
            $stdClass = Yaml::parse($contentOfFile, 1);
            $decodedFile = (array)$stdClass;
            break;
        default:
            throw new Exception('Unexpected extension');
    }
    return $decodedFile;
}

function genDiff(string $firstFile, string $secondFile): string
{
    $decodedFirstFile = fileDecode($firstFile);
    $decodedSecondFile = fileDecode($secondFile);

    return format('stylish', $decodedFirstFile, $decodedSecondFile);
}
