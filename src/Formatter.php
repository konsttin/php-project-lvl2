<?php

namespace Differ\Formatter;

use Exception;

use function Differ\Formatters\Stylish\getStylishOutput;
use function Differ\Formatters\Plain\getPlainOutput;
use function Differ\Formatters\Json\getJsonOutput;

/**
 * @throws \JsonException
 * @throws Exception
 */
function getFormatOutput(string $formatName, mixed $fileAST): string
{
    return match ($formatName) {
        'stylish' => getStylishOutput($fileAST),
        'plain' => getPlainOutput($fileAST),
        'json' => getJsonOutput($fileAST),
        default => throw new Exception('Unexpected format name')
    };
}
