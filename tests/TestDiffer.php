<?php

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\Differ\genDiff;

class TestDiffer extends TestCase
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

        $this->assertEquals('{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}', genDiff($firstFile, $secondFile));
    }
}
