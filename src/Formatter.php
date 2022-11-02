<?php

namespace src\Formatter;

use Exception;

use function src\formatters\Stylish\stylish;

function format(string $formatName, mixed $decodedFirstFile, mixed $decodedSecondFile): string
{
    return match ($formatName) {
        'stylish' => stylish($decodedFirstFile, $decodedSecondFile),
//        'plain' => plain($decodedFirstFile, $decodedSecondFile),
        default => throw new Exception('Unexpected format name')
    };
}
