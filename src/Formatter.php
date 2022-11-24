<?php

namespace src\Formatter;

use Exception;

use function src\formatters\Stylish\stylish;
use function src\formatters\Plain\plain;
use function src\formatters\Json\json;

function format(string $formatName, mixed $fileAST): string
{
    return match ($formatName) {
        'stylish' => stylish($fileAST),
        'plain' => plain($fileAST),
        'json' => json($fileAST),
        default => throw new Exception('Unexpected format name')
    };
}
