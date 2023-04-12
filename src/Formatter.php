<?php

namespace Differ\Formatter;

use Exception;

use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Json\json;

/**
 * @throws \JsonException
 * @throws Exception
 */
function getFormatOutput(string $formatName, mixed $fileAST): string
{
    return match ($formatName) {
        'stylish' => stylish($fileAST),
        'plain' => plain($fileAST),
        'json' => json($fileAST),
        default => throw new Exception('Unexpected format name')
    };
}
