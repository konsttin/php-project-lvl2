<?php

namespace Differ\Formatter;

use Exception;

use function Differ\Formatters\Stylish\getOutput as getStylishOutput;
use function Differ\Formatters\Plain\getOutput as getPlainOutput;
use function Differ\Formatters\Json\getOutput as getJsonOutput;

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
