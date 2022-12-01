<?php

namespace tests\DifferTest;

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
     */
    public function testDiffer(string $expected, string $filePath1, string $filePath2, string $formatName): void
    {
        $expectedPath = __DIR__ . '/../' . $expected;
        $file1FullPath = __DIR__ . '/../' . $filePath1;
        $file2FullPath = __DIR__ . '/../' . $filePath2;

        $this->assertStringEqualsFile($expectedPath, genDiff($file1FullPath, $file2FullPath, $formatName));
    }

    public function additionProvider(): array
    {
        $resultStylish = 'tests/fixtures/resultStylish';
        $resultPlain = 'tests/fixtures/resultPlain';
        $resultJson = 'tests/fixtures/resultJson';

        $file1JsonPath = 'tests/fixtures/file1.json';
        $file2JsonPath = 'tests/fixtures/file2.json';
        $file1YmlPath = 'tests/fixtures/file1.yml';
        $file2YamlPath = 'tests/fixtures/file2.yaml';

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
