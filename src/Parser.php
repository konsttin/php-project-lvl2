<?php

namespace src\Parser;

use Symfony\Component\Yaml\Yaml;
use Exception;

function fileDecode(string $fullFilePath): mixed
{
    $extension = pathinfo($fullFilePath, PATHINFO_EXTENSION);

    $contentOfFile = file_get_contents($fullFilePath);
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
