<?php

namespace Differ\DifferTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     * @param string $file1
     * @param string $file2
     * @param string $expected
     * @param string $formatName
     * @throws \JsonException
     */
    public function testDiffer(string $expected, string $file1, string $file2, string $formatName): void
    {
        $expectedPath = getFixturePath($expected);
        $file1Path = getFixturePath($file1);
        $file2Path = getFixturePath($file2);

        $this->assertStringEqualsFile($expectedPath, genDiff($file1Path, $file2Path, $formatName));
    }

    public function additionProvider(): mixed
    {
        return [
            'jsonStylish' => ['resultStylish', 'file1.json', 'file2.json', 'stylish'],
            'ymlStylish' => ['resultStylish', 'file1.yml', 'file2.yaml', 'stylish'],
            'jsonPlain' => ['resultPlain', 'file1.json', 'file2.json', 'plain'],
            'ymlPlain' => ['resultPlain', 'file1.yml', 'file2.yaml', 'plain'],
            'jsonJson' => ['resultJson', 'file1.json', 'file2.json', 'json'],
            'ymlJsom' => ['resultJson', 'file1.yml', 'file2.yaml', 'json']
        ];
    }
}

function getFixturePath(string $fixtureName): string
{
    return __DIR__ . '/../tests/fixtures/' . $fixtureName;
}
