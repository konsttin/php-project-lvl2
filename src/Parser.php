<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;
use Exception;

/**
 * @param string $contentOfFile
 * @param string $extensionFile
 * @return mixed
 * @throws \JsonException
 * @throws Exception
 */
function parseFile(string $contentOfFile, string $extensionFile): mixed
{
    switch ($extensionFile) {
        case 'json':
            $decodedFile = json_decode($contentOfFile, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);
            break;
        case 'yaml':
        case 'yml':
            $decodedFile = Yaml::parse($contentOfFile);
            break;
        default:
            throw new Exception('Unexpected extension');
    }
    return $decodedFile;
}

/**
 * @param string $filePath
 * @return string
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

/**
 * @param string $filePath
 * @return string
 */
function getFileExtension(string $filePath): string
{
    return pathinfo($filePath, PATHINFO_EXTENSION);
}
