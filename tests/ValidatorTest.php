<?php declare (strict_types=1);

use Jelmergu\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{

    /** @dataProvider numeric_values_are_accepted */
    public function test_numeric_values_are_accepted($set, $indices, $expected)
    {
        $this->assertEquals($expected, Validator::areNumeric($set, $indices));
    }

    public function test_either_key_is_set()
    {
        $items         = ["hello" => "hello", "world" => "world", "test" => "test"];
        $keyPresent    = ["test", "notPresent"];
        $keyNotPresent = ["notPresent", "alsoNotPresent"];

        $arrayContainsKey      = Validator::eitherKey($items, $keyPresent);
        $arrayDoesntContainKey = Validator::eitherKey($items, $keyNotPresent);

        $this->assertTrue($arrayContainsKey);

        $this->assertFalse($arrayDoesntContainKey);
    }

    public function test_array_contains_test()
    {
        $items        = ['hello', 'world', 'test'];
        $invalidItems = ['invalid', 'abc'];

        $key = 'test';

        $arrayContainsKey              = Validator::either($key, $items);
        $invalidArrayDoesNotContainKey = Validator::either($key, $invalidItems);

        $this->assertTrue($arrayContainsKey);

        $this->assertFalse($invalidArrayDoesNotContainKey);
    }

    /** @dataProvider allKeySetProvider */
    public function test_all_required_keys_are_set($set, $indexes, $expected)
    {
        $this->assertEquals($expected, Validator::areSet($set, $indexes));
    }

    /** @dataProvider all_required_keys_are_type */
    public function test_all_required_keys_are_type($set, $indices, $expected)
    {
        $this->assertEquals($expected, Validator::areMixed($set, $indices));
    }

    /**
     * @dataProvider validate_email_with_quotes
     * @dataProvider validate_email_with_length
     * @dataProvider validate_email_with_ip_addresses
     */
    public function test_email($email, $expected)
    {
        $this->assertEquals($expected, Validator::validateMail($email));
    }

    /** @dataProvider is_field_correct */
    public function test_is_field_correct($value, $validator, $expected)
    {
        $this->assertEquals($expected, Validator::is($value, $validator));
    }

    public function test_is_object_or_array()
    {
        $array  = [];
        $object = new Validator();
        $int    = 101;

        $arrayIsValidObjectOrArray  = Validator::objectOrArray($array);
        $objectIsValidObjectOrArray = Validator::objectOrArray($object);
        $intIsInvalidObejctOrArray  = Validator::objectOrArray($int);

        $this->assertTrue($arrayIsValidObjectOrArray);
        $this->assertTrue($objectIsValidObjectOrArray);
        $this->assertFalse($intIsInvalidObejctOrArray);
    }

    public function test_validate_iban()
    {
        $validIBAN       = Validator::validateIBAN("NL20INGB0001234567");
        $invalidChecksum = Validator::validateIBAN("NL19INGB0001234567");

        $this->assertTrue($validIBAN);
        $this->assertFalse($invalidChecksum);
    }

    public function test_validate_mod97()
    {
        $validNumberShouldReturnTrue = Validator::validateMod97("98");
        $invalidNumberNotMod97       = Validator::validateMod97("99");
        $inputStringIsNotANumber     = Validator::validateMod97("abc");

        $this->assertTrue($validNumberShouldReturnTrue);
        $this->assertFalse($invalidNumberNotMod97);
        $this->assertFalse($inputStringIsNotANumber);
    }

    public function test_validate_luhn_mod10()
    {
        $validNumberShouldReturnTrue    = Validator::validateLuhnMod10("18");
        $invalidNumberShouldReturnFalse = Validator::validateLuhnMod10("11");
        $inputStringIsNotANumber = Validator::validateLuhnMod10("abc");

        $this->assertTrue($validNumberShouldReturnTrue);
        $this->assertFalse($invalidNumberShouldReturnFalse);
        $this->assertFalse($inputStringIsNotANumber);
    }

    /** @dataProvider validate_creditcard_number */
    public function test_validate_creditcardnumber($number, $expected)
    {
        $this->assertEquals($expected, Validator::validateCreditcardNumber($number));
    }

    public function test_setIfEmpty()
    {
        $array = [2 => "originalValue"];

        Validator::setIfEmpty($array, 2, "unexpectedValue");
        Validator::setIfEmpty($array, "key1", "someKey");
        Validator::setIfEmpty($array, 1);

        $this->assertEquals(["key1" => "someKey", 1 => "", 2 => "originalValue"], $array);
    }

    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method     = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function all_required_keys_are_type()
    {
        yield 'value on index match types' => [
            [
                0 => 'hello',
                2 => 'World',
                3 => 'test',
                4 => 'someTest',
            ],
            [0 => Validator::STRING, 2 => "is_string", 3 => Validator::NOT_NUMERIC, 4 => "someTest"],
            true,
        ];
        yield 'value on index is not of type' => [
            [
                3 => 'tester',
            ],
            [3 => Validator::NUMERIC],
            false,
        ];
        yield 'key is not set' => [
            [
                3 => 'tester',
            ],
            [4 => Validator::NUMERIC],
            false,
        ];
        yield 'key is not set, but also not numeric' => [
            [
                3 => 'tester',
            ],
            [4 => Validator::NOT_NUMERIC],
            true,
        ]; // true because null is not numeric
        yield 'value on index is empty' => [
            [
                3 => '',
            ],
            [3 => Validator::NUMERIC],
            false,
        ]; // Empty string is not considered numeric
    }


    public function allKeySetProvider()
    {
        yield "all indices present" => [
            [
                0 => 'hello',
                3 => 'World',
                4 => 'test',
            ],
            [0, 3, 4],
            true,
        ];
        yield "requested index not present" => [
            [3 => 'tester',],
            [2],
            false,
        ];
    }

    public function numeric_values_are_accepted()
    {
        yield 'numeric succes' => [[3, 5, 6, 7], [0, 1, 2, 3], true];
        yield 'Not numeric' => [['a', 'd', 'b'], [0, 1, 2, 3, 4], false];
    }

    public function validate_email_with_quotes()
    {
        yield 'valid email containing quotes' => ['"much.more unusual"@example.com', true];
        // yield 'valid email with multiple quotes' => ['"much".more."unusaul".email@example.com', true];
        yield 'email missing quote' => ['a"b(c)d,e:f;g<h>i[j\k]l@example.com', false];
        yield 'multi quoted email missing first dot' => ['"a"b"(c)d,e".:f;g<h>i[j\k]l@example.com', false];
        yield 'multi quoted email missing last dot' => ['"a"b."(c)d,e":f;g<h>i[j\k]l@example.com', false];
    }

    public function validate_email_with_length()
    {
        yield 'length of domain to long' => ["a@abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz", false];
        yield 'length of local to long' => ["abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz@a.a", false];
        yield 'length of both to long' => ["abcdefghijklmnopqrstuvwxyz@abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz", false];
    }

    public function validate_email_with_ip_addresses()
    {
        yield 'email with valid IPv4'           => ["a@[6.6.8.8]", true];
        yield 'email with valid IPv6'           => ["a@[IPv6:::1]", true];
        yield 'email with IPv4 missing brackets' => ["a@127.0.0.1", false]; // missing square brackets around valid ip
        yield 'email with IPv4 plain invalid'    => ["a@[256.0.0.300]", false];
        yield 'email with IPv6 plain invalid'    => ["a@[IPv6:::1::0]", false]; // only one instance of :: allowed to replace 0's
        yield 'email with IPv6 missing IPv6'     => ["a@[::1]", false];
    }

    public function is_field_correct() {
        yield 'field is empty as expected' => ["", Validator::EMPTY, true];
        yield 'field is boolean and true as expected' => [true, Validator::TRUE, true];
        yield 'field is not a callable' => ["hi", "!is_callable", true];
        yield 'field is callable as expected' => [function () { return true;}, "is_callable", true];

        yield 'field is expected to be empty was not' => ["hello", Validator::EMPTY, false];
        yield 'field is expected to be true was not' => ["hello", Validator::TRUE, false];
        yield 'field is callable, while not callable expected' => [function () { return true;}, "!is_callable", false];
        yield 'field is not callable, while expected to be' => ["hi", "is_callable", false];
    }

    public function validate_creditcard_number() {
        yield 'valid creditcard number' => ["5209530664489287", true];
        yield 'invalid creditcard number' => ["5209530664489288", false];
        yield 'creditcard number is to long' => ["52095306644892888888", false];
        yield 'creditcard number is to short' => ["5209", false];
        yield 'creditcard number is not a number' => ["abcdefghijklmnop", false];

    }
}

