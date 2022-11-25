<?php

namespace tests\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;
use function src\Parser\getFullFilePath;

class DifferTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     * @param string $filePath1
     * @param string $filePath2
     * @param string $expected
     * @param string $formatName
     */
    public function testDiffer(string $expected, string $filePath1, string $filePath2, string $formatName): void
    {
        $this->assertEquals($expected, genDiff($filePath1, $filePath2, $formatName));
    }

    public function additionProvider(): mixed
    {
        $resultStylish = file_get_contents(getFullFilePath('tests/fixtures/resultStylish'));
        $resultPlain = file_get_contents(getFullFilePath('tests/fixtures/resultPlain'));
        $resultJson = file_get_contents(getFullFilePath('tests/fixtures/resultJson'));

        $file1JsonPath = getFullFilePath('tests/fixtures/file1.json');
        $file2JsonPath = getFullFilePath('tests/fixtures/file2.json');
        $file1YmlPath = getFullFilePath('tests/fixtures/file1.yml');
        $file2YamlPath = getFullFilePath('tests/fixtures/file2.yaml');
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
