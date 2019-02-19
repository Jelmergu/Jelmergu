<?php


use Jelmergu\DatabaseConnectors\MySQL;
use Jelmergu\DatabaseConnectors\PostgreSQL;
use Jelmergu\Exceptions\ConstantNotSetException;
use PHPUnit\Framework\TestCase;


class DSNConstructorsTest extends TestCase
{

    public function providerDriverList() : array
    {
        return [
            ["Jelmergu\DatabaseConnectors\PostgreSQL"],
            ["Jelmergu\DatabaseConnectors\MySQL"],
        ];
    }

    public function providerDriverDSNs() : array
    {
        return [
            ["Jelmergu\DatabaseConnectors\PostgreSQL", "pgsql:host=host;dbname=name;charset=charset;port=port"],
            ["Jelmergu\DatabaseConnectors\MySQL", "mysql:host=host;dbname=name;charset=charset;port=port"],
        ];
    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     *
     * @dataProvider        providerDriverList
     *
     */
    public function test_ExceptionThrown($driver)
    {
        $this->expectException(ConstantNotSetException::class);
        $dsn = (new $driver())->getDSN();
    }

    /**
     * @depends test_ExceptionThrown
     * @dataProvider providerDriverDSNs
     *
     */
    public function test_correctDSN($driver, $expected)
    {
        $constants = [
            "DB_HOST" => "host",
            "DB_NAME" => "name",
            "DB_CHARSET" => "charset",
            "DB_PORT" => "port",
        ];

        foreach ($constants as $key => $value) {
            if (!defined($key)) {
                define($key, $value);
            }
        }
        $this->assertEquals($expected, (new $driver())->getDSN());
    }

}
