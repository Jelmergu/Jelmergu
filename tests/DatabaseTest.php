<?php


use Jelmergu\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    // /**
    //  * @var  Database $mockForDatabaseTrait
    //  */
    // protected $mockForDatabaseTrait;
    //
    // protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    // {
    //     $this->mockForDatabaseTrait = $this->getMockForTrait(Database::class);
    //
    //     Database::$DatabaseOptions['log'] = 0;
    // }
    //
    //
    // /**
    //  * @dataProvider parameterizeProvider
    //  *
    //  * @return void
    //  */
    // public function test_parameterize_query($query, $result) {
    //     $parameters = [
    //         "a" => "hello",
    //         ":b" => "world",
    //     ];
    //
    //     $args = [$query, &$parameters];
    //     $resultingObject = $this->invokeMethod($this->mockForDatabaseTrait, "parametrize", $args);
    //
    //     $this->assertEquals($result, $parameters);
    //
    //     $this->assertEquals($this->mockForDatabaseTrait, $resultingObject);
    // }
    //
    // public function parameterizeProvider() {
    //     return [
    //             ["SELECT * FROM test WHERE a = :a", [":a" => "hello"]],
    //             ["SELECT * FROM test WHERE a = :b", [":b" => "world"]],
    //         ];
    // }
    //
    // /**
    //  * @dataProvider fillQueryProvider
    //  */
    // public function test_fillQuery($query, $result) {
    //     $parameters = [
    //         "a" => "hello",
    //         ":b" => "world",
    //     ];
    //
    //     $this->assertEquals($result, $this->mockForDatabaseTrait->fillQuery($query, $parameters));
    // }
    //
    // public function fillQueryProvider() {
    //     return [
    //         ["SELECT * FROM test WHERE a = :a", "SELECT * FROM test WHERE a = 'hello'"],
    //         ["SELECT * FROM test WHERE a = :b", "SELECT * FROM test WHERE a = 'world'"],
    //     ];
    // }
    //
    // public function invokeMethod(&$object, $methodName, array &$parameters = [])
    // {
    //     $reflection = new \ReflectionClass(get_class($object));
    //     $method     = $reflection->getMethod($methodName);
    //     $method->setAccessible(true);
    //
    //     return $method->invokeArgs($object, $parameters);
    // }

}