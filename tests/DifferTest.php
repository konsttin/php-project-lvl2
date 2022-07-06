<?php

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\genDiff;

class DifferTest extends TestCase
{
    public function testDiffer()
    {
        $result = file_get_contents(__DIR__ . '/../tests/fixtures/result');

        $this->assertEquals($result, genDiff('fixtures/file1.json', 'fixtures/file2.json'));
    }
}
