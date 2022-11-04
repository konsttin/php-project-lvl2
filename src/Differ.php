<?php

namespace Hexlet\Code;

use function src\Formatter\format;
use function src\Parser\fileDecode;
use function src\MakerAST\makeAST;

function genDiff(string $firstFilePath, string $secondFilePath, string $format = 'stylish'): string
{
    $decodedFirstFile = fileDecode($firstFilePath);
    $decodedSecondFile = fileDecode($secondFilePath);
    $fileAST = makeAST($decodedFirstFile, $decodedSecondFile);

    return format($format, $fileAST);
}
