<?php

namespace Differ\Differ;

use function Differ\Formatter\getFormatOutput;
use function Differ\Parser\getDecodedFile;
use function Differ\MakerAST\makeAST;

/**
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
