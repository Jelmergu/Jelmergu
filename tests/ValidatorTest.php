<?php

use Jelmergu\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function test_numeric_values_are_accepted()
    {
        $numbersAreNumeric = Validator::areNumeric([3, 5, 6, 7], [0, 1, 2, 3]);

        $nonNumericValuesAreNumeric = Validator::areNumeric(['a', 'd', 'b'], [0, 1, 2, 3, 4]);

        $this->assertTrue($numbersAreNumeric);

        $this->assertFalse($nonNumericValuesAreNumeric);
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

    public function test_all_required_keys_are_set()
    {
        $arrayKeysExist = Validator::areSet(
            [
                0 => 'hello',
                3 => 'World',
                4 => 'test',
            ],
            [0, 3, 4]);

        $arrayWithMissingKeys = Validator::areSet(
            [
                3 => 'tester',
            ],
            [2]);

        $this->assertTrue($arrayKeysExist);

        $this->assertFalse($arrayWithMissingKeys);
    }

    public function test_all_required_keys_are_type()
    {
        $arrayKeysAreType = Validator::areMixed(
            [
                0 => 'hello',
                2 => 'World',
                3 => 'test',
                4 => 'someTest',
            ],
            [0 => Validator::STRING, 2 => "is_string", 3 => Validator::NOT_NUMERIC, 4 => "someTest"]);

        $arrayKeysAreNotType = Validator::areMixed(
            [
                3 => 'tester',
            ],
            [3 => Validator::NUMERIC]);

        $arrayKeysAreNotSet = Validator::areMixed(
            [
                3 => 'tester',
            ],
            [4 => Validator::NUMERIC]);

        $arrayKeysAreNotEmpty = Validator::areMixed(
            [
                3 => '',
            ],
            [3 => Validator::NUMERIC]);

        $this->assertTrue($arrayKeysAreType);

        $this->assertFalse($arrayKeysAreNotType);

        $this->assertFalse($arrayKeysAreNotSet);

        $this->assertFalse($arrayKeysAreNotEmpty);
    }

    public function test_email_with_quotes()
    {
        $validEmail = Validator::validateMail('"much.more unusual"@example.com');

        $invalidEmail = Validator::validateMail('a"b(c)d,e:f;g<h>i[j\k]l@example.com');

        $this->assertTrue($validEmail);

        $this->assertFalse($invalidEmail);
    }

    public function test_email_correct_length()
    {
        $domainToLong = "a@abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz";
        $localToLong = "abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz@a.a";
        $completelyToLong = "abcdefghijklmnopqrstuvwxyz@abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz";

        $this->assertFalse(Validator::validateMail($domainToLong));
        $this->assertFalse(Validator::validateMail($localToLong));
        $this->assertFalse(Validator::validateMail($completelyToLong));
    }

    public function test_email_with_ips() {
        $validIPv4 = "a@[6.6.8.8]";
        $validIPv6 = "a@[IPv6:::1]";

        $IPv4Private = "a@[127.0.0.1]";
        $IPv4MissingBrackets = "a@127.0.0.1"; // missing square brackets around valid ip
        $IPv4PlainInvalid = "a@[256.0.0.300]";

        $IPv6PlainInvalid = "a@[IPv6:::1::0]"; // only one instance of :: allowed to replace 0's
        $IPv6MissingIPv6 = "a@[::1]";

        $this->assertTrue(Validator::validateMail($validIPv4));
        $this->assertTrue(Validator::validateMail($validIPv6));

        $this->assertFalse(Validator::validateMail($IPv4Private));
        $this->assertFalse(Validator::validateMail($IPv4MissingBrackets));
        $this->assertFalse(Validator::validateMail($IPv4PlainInvalid));

        $this->assertFalse(Validator::validateMail($IPv6MissingIPv6));
        $this->assertFalse(Validator::validateMail($IPv6PlainInvalid));
    }

    public function test_is_field_correct()
    {
        $items = [
            "empty"       => "",
            "boolTrue"    => true,
            "boolFalse"   => false,
            "is_callable" => "is_callable",
        ];

        $validFields = [
            "empty"       => Validator::EMPTY,
            "boolTrue"    => Validator::TRUE,
            "boolFalse"   => Validator::FALSE,
            "is_callable" => "!is_callable",
        ];

        $invalidFields = [
            "empty"       => Validator::NOT_EMPTY,
            "boolTrue"    => Validator::FALSE,
            "boolFalse"   => Validator::TRUE,
            "is_callable" => "is_callable",
        ];

        $validFieldIsEmpty   = Validator::is("", Validator::EMPTY);
        $invalidFieldIsEmpty = Validator::is("hello", Validator::EMPTY);

        $validFieldIsTrue   = Validator::is(true, Validator::TRUE);
        $invalidFieldIsTrue = Validator::is("hello", Validator::TRUE);

        // $validFieldIsNotCallable = Validator::is("hi", "!is_callable");
        // $invalidFieldIsNotCallable = Validator::is(function () { return true;}, "!is_callable");

        $validFieldIsCallable   = Validator::is(function () {
            return true;
        }, "is_callable");
        $invalidFieldIsCallable = Validator::is("hi", "is_callable");

        $this->assertTrue($validFieldIsEmpty);
        $this->assertFalse($invalidFieldIsEmpty);

        $this->assertTrue($validFieldIsTrue);
        $this->assertFalse($invalidFieldIsTrue);

        $this->assertTrue($validFieldIsCallable);
        $this->assertFalse($invalidFieldIsCallable);

        // $this->assertTrue($validFieldIsNotCallable);
        // $this->assertFalse($invalidFieldIsNotCallable);
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
}

