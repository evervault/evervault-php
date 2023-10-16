<?php

namespace Evervault\Tests\EndToEnd;

use Evervault\Tests\EndToEnd\EndToEndTestCase;

class EncryptTest extends EndToEndTestCase {

    public function testEncryptTrue()
    {
        $bool = true;
        $encrypted = self::$evervaultClient->encrypt($bool);
        $decrypted = self::$evervaultClient->decrypt($encrypted);
        $this->assertTrue($decrypted);
    }

    public function testEncryptFalse()
    {
        $bool = false;
        $encrypted = self::$evervaultClient->encrypt($bool);
        $decrypted = self::$evervaultClient->decrypt($encrypted);
        $this->assertFalse($decrypted);
    }
    public function testEncryptString()
    {
        $string = 'Hello World!';
        $encrypted = self::$evervaultClient->encrypt($string);
        $decrypted = self::$evervaultClient->decrypt($encrypted);
        $this->assertEquals($string, $decrypted);
    }

    public function testEncryptInteger()
    {
        $number = 1234567;
        $encrypted = self::$evervaultClient->encrypt($number);
        $decrypted = self::$evervaultClient->decrypt($encrypted);
        $this->assertEquals($number, $decrypted);
    }

    public function testEncryptFloat()
    {
        $number = 123.45;
        $encrypted = self::$evervaultClient->encrypt($number);
        $decrypted = self::$evervaultClient->decrypt($encrypted);
        $this->assertEquals($number, $decrypted);
    }

    public function testEncryptArray()
    {
        $number = ["apple", 12345, 123.45, true, false];
        $encrypted = self::$evervaultClient->encrypt($number);
        $decrypted = self::$evervaultClient->decrypt($encrypted);
        $this->assertEquals($number, $decrypted);
    }

    public function testEncryptAssociativeArray()
    {
        $obj = [
            "string" => "apple",
            "integer" => 12345,
            "float" => 123.45,
            "true" => true,
            "false" => false,
            "array" => ["apple", 12345, 123.45, true, false]
        ];
        $encrypted = self::$evervaultClient->encrypt($obj);
        $decrypted = self::$evervaultClient->decrypt($encrypted);
        $this->assertEquals($obj, $decrypted);
    }
}