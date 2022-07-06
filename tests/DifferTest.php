<?php

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\genDiff;

class DifferTest extends TestCase
{
    public function testDiffer()
    {
        $firstFile = '{
  "host": "hexlet.io",
  "timeout": 50,
  "proxy": "123.234.53.22",
  "follow": false
}';

        $secondFile = '{
  "timeout": 20,
  "verbose": true,
  "host": "hexlet.io"
}';
        $result = file_get_contents(__DIR__ . "/../fixtures/result.php");

        $this->assertEquals($result, genDiff($firstFile, $secondFile));
    }
}
