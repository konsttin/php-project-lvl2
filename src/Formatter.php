<?php

namespace Differ\Formatter;

use Exception;

use function Differ\Formatters\Stylish\getFormatValue as getFormatStylish;
use function Differ\Formatters\Plain\getFormatValue as getFormatPlain;
use function Differ\Formatters\Json\getFormatValue as getFormatJson;

/**
 * @throws \JsonException
 * @throws Exception
 */
function getFormatOutput(string $formatName, mixed $fileAST): string
{
    return match ($formatName) {
        'stylish' => getFormatStylish($fileAST),
        'plain' => getFormatPlain($fileAST),
        'json' => getFormatJson($fileAST),
        default => throw new Exception('Unexpected format name')
    };
}
