<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\genDiff;

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
        $resultStylish = file_get_contents(__DIR__ . '/../tests/fixtures/resultStylish');
        $resultPlain = file_get_contents(__DIR__ . '/../tests/fixtures/resultPlain');
        return [
            'jsonStylish' => [$resultStylish, 'tests/fixtures/file1.json', 'tests/fixtures/file2.json', 'stylish'],
            'ymlStylish' => [$resultStylish, 'tests/fixtures/file1.yml', 'tests/fixtures/file2.yaml', 'stylish'],
            'jsonPlain' => [$resultPlain, 'tests/fixtures/file1.json', 'tests/fixtures/file2.json', 'plain'],
            'ymlPlain' => [$resultPlain, 'tests/fixtures/file1.yml', 'tests/fixtures/file2.yaml', 'plain']
        ];
    }
}
