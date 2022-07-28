<?php

namespace Hexlet\Code;

use Exception;

use function src\Parser\parser;

function genDiff(string $firstFile, string $secondFile): string
{
    $contentOfFirstFile = file_get_contents(__DIR__ . '/../' . $firstFile);
    if ($contentOfFirstFile === false) {
        throw new Exception();
    }

    $contentOfSecondFile = file_get_contents(__DIR__ . '/../' . $secondFile);
    if ($contentOfSecondFile === false) {
        throw new Exception();
    }

    $decodedFirstFile = json_decode($contentOfFirstFile, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);
    $decodedSecondFile = json_decode($contentOfSecondFile, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);

    return parser($decodedFirstFile, $decodedSecondFile);
}
