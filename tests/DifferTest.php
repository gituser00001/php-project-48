<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\gendiff;

class DifferTest extends TestCase
{
    public function testGendiff(): void
    {
        $expected = file_get_contents("tests/fixtures/SucsessDiffer.txt");
        $this->assertEquals($expected, gendiff("tests/fixtures/file1.json", "tests/fixtures/file2.json"));

        $expected2 = file_get_contents("tests/fixtures/SucsessDifferYml.txt");
        $this->assertEquals($expected2, gendiff("tests/fixtures/file1.yml", "tests/fixtures/file2.yml"));
    }
}
