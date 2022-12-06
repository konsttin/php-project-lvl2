<?php

namespace Differ\Formatters\Json;

/**
 * @throws \JsonException
 */
function getJsonOutput(mixed $fileAST): string
{
    $result = json_encode($fileAST, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    echo($result);
    return $result;
}
