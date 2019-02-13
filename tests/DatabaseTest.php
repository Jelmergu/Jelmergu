<?php declare (strict_types=1);


use Jelmergu\Database;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    /** @var $pdoMock MockBuilder */
    public $pdoMock;

    public function setUp()
    {
        $this->pdoMock = $this->getMockBuilder('PDOMock')
                              ->disableOriginalConstructor();
    }

    public function test_supportedDrivers()
    {
        $supported = [
            "MySQL",
            "PostgreSQL",
        ];
        $drivers   = PDO::getAvailableDrivers();

        $expected = [];
        foreach ($supported as $value) {
            if (in_array(strtolower($value), $drivers)) {
                $expected[] = $value;
            }
        }
        $this->assertEquals($expected, Database::getSupportedTypes(), "", 0.0, 2, true);
    }

    public function test_prepare()
    {
        /**
         * @var $mock MockObject
         */
        $mock = $this->pdoMock->setMethods(['prepare'])->getMock();
        $mock->expects($this->once())
             ->method("prepare")
             ->with($this->callback(
                 function ($query) {
                     return "SELECT `a` FROM `b`" == $query;
                 })
             )
             ->will($this->returnCallback(function ($arg) {
                 return new PDOStatement();
             }));

        DatabaseCover::setPDO($mock);

        $result = DatabaseCover::prepare("SELECT `a` FROM `b`");

        $this->assertInstanceOf(PDOStatement::class, $result);
    }

    public function tearDown()
    {
        $this->pdoMock = null;
        DatabaseCover::clearPDO();
    }

    public function test_getTransactionThrowsException()
    {
        $this->expectException("\Jelmergu\Exceptions\ConstantNotSetException");
        Database::getTransaction();
    }

    public function test_transactions()
    {
        $mock = $this->pdoMock->setMethods(['inTransaction', 'beginTransaction', 'commit', 'rollback'])->getMock();
        $mock->expects($this->atLeast(1))->method("inTransaction")->will($this->onConsecutiveCalls(false, false, true, true, true, true, false, true, true, true));
        $mock->expects($this->atLeast(2))->method('beginTransaction');
        $mock->expects($this->once())->method('commit');
        $mock->expects($this->once())->method('rollback');

        DatabaseCover::setPDO($mock);

        $this->assertFalse(DatabaseCover::getTransaction()); // first call to inTransaction
        DatabaseCover::transaction(); // begin of transaction second call to inTransaction
        $this->assertTrue(DatabaseCover::getTransaction()); // third call to inTransaction
        $this->assertTrue(DatabaseCover::transactionSucceeds()); // fourth call to inTransaction
        DatabaseCover::transaction(); // end of transaction fifth and sixth call to inTransaction
        DatabaseCover::transaction(); // begin of transaction seventh call to inTransaction
        DatabaseCover::setTransactionError(true);
        $this->assertFalse(DatabaseCover::transactionSucceeds()); // eight call to inTransaction
        DatabaseCover::transaction(); // end of transaction ninth and tenth call to inTransaction
    }

    public function test_prepareSettingsString() {
        define("DB_HOST", "test");
        define("DB_NAME", "test");
        define("DB_TYPE", "MySQL");

        $this->assertEquals("mysql:host=test;dbname=test", DatabaseCover::prepareSettingsString());

        define("DB_PORT", 10);
        $this->assertEquals("mysql:host=test;dbname=test;port=10", DatabaseCover::prepareSettingsString());

    }

    // public function test_execute() {
    //     $statementMock = $this->getMockBuilder("PDOStatement")->setMethods(['execute'])->disableOriginalConstructor()->getMock();
    //     $statementMock->expects($this->exactly(2))->method('execute')->willReturn(true);
    //
    //     $pdoMock = $this->pdoMock->setMethods(['prepare'])->getMock();
    //     $pdoMock->expects($this->atLeast(1))->method('prepare')->willReturn($statementMock);
    //
    //     DatabaseCover::setPDO($pdoMock);
    //     $statement = "SELECT a FROM b";
    //     DatabaseCover::execute($statement);
    //
    // }

    /**
     * @dataProvider parameterizeProvider
     *
     * @return void
     */
    public function test_parameterize_query($query, $result)
    {
        $parameters = [
            "a"  => "hello",
            ":b" => "world",
        ];

        Database::parametrize($query, $parameters);

        $this->assertEquals($result, $parameters);
    }

    public function parameterizeProvider() : array
    {
        return [
            ["SELECT * FROM `test` WHERE `a` = :a", [":a" => "hello"]],
            ["SELECT * FROM `test` WHERE `a` = :b", [":b" => "world"]],
            ["SELECT * FROM `test` WHERE `a` = :c", []],
        ];
    }

    /**
     * @dataProvider fillQueryProvider
     */
    public function test_fillQuery($query, $result)
    {
        $parameters = [
            "a"  => "hello",
            ":b" => "world",
        ];

        $this->assertEquals($result, Database::fillQuery($query, $parameters));
    }

    public function fillQueryProvider() : array
    {
        return [
            ["SELECT * FROM `test` WHERE `a` = :a", "SELECT * FROM `test` WHERE `a` = 'hello'"],
            ["SELECT * FROM `test` WHERE `a` = :b", "SELECT * FROM `test` WHERE `a` = 'world'"],
        ];
    }

}

class DatabaseCover extends Database
{
    public static function setPDO($pdo)
    {
        self::$db = $pdo;
    }

    public static function clearPDO()
    {
        self::$db = null;
    }

    public static function setTransactionError(bool $value)
    {
        self::$noTransactionErrors = !$value;
    }
}

class PDOMock extends \PDO
{
    public function __construct()
    {
    }
}