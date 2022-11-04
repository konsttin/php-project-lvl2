<?php

namespace Hexlet\Code;

use function src\Formatter\format;
use function src\Parser\fileDecode;
use function src\MakerAST\makeAST;

function genDiff(string $firstFile, string $secondFile, string $format = 'stylish'): string
{
    $decodedFirstFile = fileDecode($firstFile);
    $decodedSecondFile = fileDecode($secondFile);
    $fileAST = makeAST($decodedFirstFile, $decodedSecondFile);

    return format($format, $fileAST);
}
