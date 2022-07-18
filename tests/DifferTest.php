<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\genDiff;

class DifferTest extends TestCase
{
    public function testDiffer(): void
    {
        $result = file_get_contents(__DIR__ . '/../tests/fixtures/result');

        $this->assertEquals($result, genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json'));
    }
}
