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
}