<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\genDiff;

class DifferTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testDiffer(string $filePath1, string $filePath2, string $expected): void
    {
        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }

    public function additionProvider(): mixed
    {
        $result = file_get_contents(__DIR__ . '/../tests/fixtures/result');
        $result2 = file_get_contents(__DIR__ . '/../tests/fixtures/result2');
        return [
            'json' => ['tests/fixtures/file1.json', 'tests/fixtures/file2.json', $result],
            'yml' => ['tests/fixtures/file1.yml', 'tests/fixtures/file2.yaml', $result],
            'json2' => ['tests/fixtures/file3.json', 'tests/fixtures/file4.json', $result2],
            //'yml2' => ['tests/fixtures/file3.yml', 'tests/fixtures/file4.yaml', $result2]
        ];
    }
}
