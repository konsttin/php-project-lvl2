<?php

namespace Differ\Differ;

use Exception;

use function Differ\Formatter\getFormatOutput;
use function Differ\MakerAST\makeAST;
use function Differ\Parser\parseFile;

/**
 * @param string $firstFilePath
 * @param string $secondFilePath
 * @param string $format
 * @return string
 * @throws \JsonException
 * @throws Exception
 */
function genDiff(string $firstFilePath, string $secondFilePath, string $format = 'stylish'): string
{
    $decodedFirstFile = getDecodedFile($firstFilePath);
    $decodedSecondFile = getDecodedFile($secondFilePath);
    $fileAST = makeAST($decodedFirstFile, $decodedSecondFile);

    return getFormatOutput($format, $fileAST);
}

/**
 * @param string $fullFilePath
 * @return mixed
 * @throws \JsonException
 * @throws Exception
 */
function getDecodedFile(string $fullFilePath): mixed
{
    $contentOfFile = getFileContent($fullFilePath);
    $extension = getFileExtension($fullFilePath);

    return parseFile($contentOfFile, $extension);
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
