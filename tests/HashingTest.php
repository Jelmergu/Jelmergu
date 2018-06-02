<?php

use Jelmergu\Hashing;
use PHPUnit\Framework\TestCase;

class HashingTest extends TestCase
{
    public function test_input_hash_is_created_with_password_hash()
    {
        $plainText = 'hello_world';

        $validHash = password_hash($plainText, PASSWORD_DEFAULT);
        $invalidHash = md5($plainText);

        $this->assertTrue(Hashing::isPasswordHash($validHash));

        $this->assertFalse(Hashing::isPasswordHash($invalidHash));
    }

    public function test_md5_of_hello_returns_bcrypt_of_hello()
    {
        $plainText = 'hello';

        $referencedMd5HashOfHello = md5($plainText);

        Hashing::md5ToPassHash($plainText, $referencedMd5HashOfHello);

        $isValidPassword = password_verify($plainText, $referencedMd5HashOfHello);

        $this->assertTrue($isValidPassword);
    }

    public function test_md5_is_not_correct() {
        $plainText = 'hello';

        $referencedMd5HashOfHello = md5($plainText."not");

        $convertationResult = Hashing::md5ToPassHash($plainText, $referencedMd5HashOfHello);

        $this->assertFalse($convertationResult);
    }

    public function test_crypt_to_password_hash() {
        $salt = "this is the salt";
        $password = "hello,";

        $correctPassword = crypt($password, $salt);
        $incorrectPassword = crypt($password, $salt);

        $validPassword = Hashing::cryptToPassHash($password, $correctPassword, $salt);
        $invalidPassword = Hashing::cryptToPassHash("No it's not", $incorrectPassword, $salt);

        $isValidPassword = password_verify($password, $correctPassword);

        $this->assertTrue($isValidPassword);
        $this->assertTrue($validPassword);

        $this->assertFalse($invalidPassword);
    }

}