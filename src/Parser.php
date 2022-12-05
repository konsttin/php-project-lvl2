<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;
use Exception;

/**
 * @throws \JsonException
 * @throws Exception
 */
function getDecodedFile(string $fullFilePath): mixed
{
    $extension = getFileExtension($fullFilePath);

    $contentOfFile = getFileContent($fullFilePath);

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

/**
 * @throws Exception
 */
function getFileContent(string $filePath): string
{
    $contentOfFile = file_get_contents($filePath);
    if ($contentOfFile === false) {
        throw new Exception('File is empty');
    }
    return $contentOfFile;
}

function getFileExtension(string $filePath): string
{
    return pathinfo($filePath, PATHINFO_EXTENSION);
}
