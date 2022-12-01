<?php

namespace src\formatters\Json;

function json(mixed $fileAST): string
{
    return json_encode($fileAST, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
}
