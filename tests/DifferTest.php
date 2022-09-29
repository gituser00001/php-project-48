<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\gendiff;

class DifferTest extends TestCase
{
    public function testGendiff(): void
    {
        $expected = file_get_contents("tests/fixtures/SucsessStylish");
        $this->assertEquals($expected, gendiff("tests/fixtures/file1.json", "tests/fixtures/file2.json"));

        $expected2 = file_get_contents("tests/fixtures/SucsessStylish");
        $this->assertEquals($expected2, gendiff("tests/fixtures/file1.yml", "tests/fixtures/file2.yml"));

        $expected3 = file_get_contents("tests/fixtures/SucsessPlain");
        $this->assertEquals($expected3, gendiff("tests/fixtures/file1.json", "tests/fixtures/file2.json", 'plain'));

        $expected4 = file_get_contents("tests/fixtures/SucsessJson");
        $this->assertEquals($expected4, gendiff("tests/fixtures/file1.json", "tests/fixtures/file2.json", 'json'));
    }
}
