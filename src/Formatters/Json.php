<?php

namespace Differ\Formatters\Json;

/**
 * @throws \JsonException
 */
function json(mixed $fileAST): string
{
    return json_encode($fileAST, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
}
