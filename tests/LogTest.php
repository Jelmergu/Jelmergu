<?php declare (strict_types=1);

use Jelmergu\Log;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class LogTest extends TestCase
{
    /**
     * @var vfsStream $fileSystem
     */
    public $fileSystem;

    public function setUp()
    {
        $directory = [
            "logs" => [
                "test.log" => ""
            ]
        ];
        $this->fileSystem = vfsStream::setup('root', 777, $directory);
        Log::setLogLocation($this->fileSystem->url()."/logs");
    }

    public function test_invalid_directory_throws_exception() {
        $this->expectException("\Jelmergu\Exceptions\FileNotFound");
        Log::setLogLocation("a directory that does not exist");
        $this->assertNotEquals("a directory that does not exist", Log::$logLocation);
    }

    public function test_write_to_log() {
        Log::writeLog("test", "hello");
        $this->assertTrue(strpos(file_get_contents($this->fileSystem->url()."/logs/test.log"), "hello") !== false);
    }

    public function test_write_array_to_log() {
        Log::writeLog("test", ["test"]);
        $this->assertTrue(strpos(file_get_contents($this->fileSystem->url()."/logs/test.log"), '["test"]') !== false);
    }

    public function test_write_multiple_to_log() {
        Log::writeLog("test", ["test"], ['test2'=>'test2']);
        $fileContents = file_get_contents($this->fileSystem->url()."/logs/test.log");
        $this->assertTrue(strpos($fileContents, '["test"]') !== false);
        $this->assertTrue(strpos($fileContents, '{"test2":"test2"}') !== false, "second array not present");
    }

    public function test_log_shorthands() {
        Log::databaseLog("test");
        $fileContents = file_get_contents($this->fileSystem->url()."/logs/database.log");
        $this->assertTrue(strpos($fileContents, 'test') !== false);
    }

}
