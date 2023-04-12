<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;
use Exception;

/**
 * @param string $content
 * @param string $extension
 * @return mixed
 * @throws \JsonException
 * @throws Exception
 */
function parseFile(string $content, string $extension): mixed
{
    return match ($extension) {
        'json' => json_decode($content, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR),
        'yaml', 'yml' => Yaml::parse($content),
        default => throw new Exception('Unexpected extension'),
    };
}
