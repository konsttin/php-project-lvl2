<?php

namespace Differ\Formatters\Json;

/**
 * @throws \JsonException
 */
function getFormatValue(mixed $fileAST): string
{
    return json_encode($fileAST, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
}
