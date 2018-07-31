<?php declare (strict_types=1);


use Jelmergu\Database;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class DatabaseTest extends TestCase
{

    public function setUp() {
        Database::$DatabaseOptions['log'] = 0;

    }

    /**
     * @dataProvider parameterizeProvider
     *
     * @return void
     */
    public function test_parameterize_query($query, $result) {
        $parameters = [
            "a" => "hello",
            ":b" => "world",
        ];


        Database::parametrize($query, $parameters);

        $this->assertEquals($result, $parameters);
    }

    public function parameterizeProvider() : array{
        return [
                ["SELECT * FROM test WHERE a = :a", [":a" => "hello"]],
                ["SELECT * FROM test WHERE a = :b", [":b" => "world"]],
                ["SELECT * FROM test WHERE a = :c", []],
            ];
    }

    /**
     * @dataProvider fillQueryProvider
     */
    public function test_fillQuery($query, $result) {
        $parameters = [
            "a" => "hello",
            ":b" => "world",
        ];

        $this->assertEquals($result, Database::fillQuery($query, $parameters));
    }

    public function fillQueryProvider() : array{
        return [
            ["SELECT * FROM test WHERE a = :a", "SELECT * FROM test WHERE a = 'hello'"],
            ["SELECT * FROM test WHERE a = :b", "SELECT * FROM test WHERE a = 'world'"],
        ];
    }
}