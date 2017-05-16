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

    public function test_array_contains_test()
    {
        $items = ['hello', 'world', 'test'];
        $invalidItems = ['invalid', 'abc'];

        $key = 'test';

        $arrayContainsKey = Validator::either($key, $items);
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

        $this->assertTrue($arrayKeysAreType);

        $this->assertFalse($arrayKeysAreNotType);
    }

    public function test_email_with_quotes()
    {
        $validEmail = Validator::validateMail('"much.more unusual"@example.com');

        $invalidEmail = Validator::validateMail('a"b(c)d,e:f;g<h>i[j\k]l@example.com');

        $this->assertTrue($validEmail);

        $this->assertFalse($invalidEmail);
    }
}

