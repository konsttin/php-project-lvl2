<?php

namespace Differ\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     * @param string $filePath1
     * @param string $filePath2
     * @param string $expected
     * @param string $formatName
     * @throws \JsonException
     */
    public function testDiffer(string $expected, string $filePath1, string $filePath2, string $formatName): void
    {
        $this->assertStringEqualsFile($expected, genDiff($filePath1, $filePath2, $formatName));
    }

    public function additionProvider(): mixed
    {
        $resultStylish = getFicsturePath('resultStylish');
        $resultPlain = getFicsturePath('resultPlain');
        $resultJson = getFicsturePath('resultJson');

        $file1JsonPath = getFicsturePath('file1.json');
        $file2JsonPath = getFicsturePath('file2.json');
        $file1YmlPath = getFicsturePath('file1.yml');
        $file2YamlPath = getFicsturePath('file2.yaml');

        return [
            'jsonStylish' => [$resultStylish, $file1JsonPath, $file2JsonPath, 'stylish'],
            'ymlStylish' => [$resultStylish, $file1YmlPath, $file2YamlPath, 'stylish'],
            'jsonPlain' => [$resultPlain, $file1JsonPath, $file2JsonPath, 'plain'],
            'ymlPlain' => [$resultPlain, $file1YmlPath, $file2YamlPath, 'plain'],
            'jsonJson' => [$resultJson, $file1JsonPath, $file2JsonPath, 'json'],
            'ymlJson' => [$resultJson, $file1YmlPath, $file2YamlPath, 'json']
        ];
    }
}

function getFicsturePath(string $ficstureName): string
{
    return __DIR__ . '/../' . 'tests/fixtures/' . $ficstureName;
}
