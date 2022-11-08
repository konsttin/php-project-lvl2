<?php

namespace src\Formatter;

use Exception;

use function src\formatters\Stylish\stylish;
use function src\formatters\Plain\plain;

function format(string $formatName, mixed $fileAST): string
{
    return match ($formatName) {
        'stylish' => stylish($fileAST),
        'plain' => plain($fileAST),
        default => throw new Exception('Unexpected format name')
    };
}
