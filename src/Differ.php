<?php

namespace Differ\Differ;

use function Differ\Formatter\getFormatOutput;
use function Differ\MakerAST\makeAST;
use function Differ\Parser\getFileContent;
use function Differ\Parser\getFileExtension;
use function Differ\Parser\parseFile;

/**
 * @param string $firstFilePath
 * @param string $secondFilePath
 * @param string $format
 * @return string
 * @throws \JsonException
 * @throws \Exception
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
 * @throws \Exception
 */
function getDecodedFile(string $fullFilePath): mixed
{
    $contentOfFile = getFileContent($fullFilePath);
    $extension = getFileExtension($fullFilePath);

    return parseFile($contentOfFile, $extension);
}
