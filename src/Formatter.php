<?php

namespace src\Formatter;

use Exception;

use function src\formatters\Stylish\stylish;

function format(string $formatName, mixed $fileAST): string
{
    return match ($formatName) {
        'stylish' => stylish($fileAST),
//        'plain' => plain($decodedFirstFile, $decodedSecondFile),
        default => throw new Exception('Unexpected format name')
    };
}
