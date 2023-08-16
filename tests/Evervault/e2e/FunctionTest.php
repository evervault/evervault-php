<?php

use PHPUnit\Framework\TestCase;
use Evervault\Evervault;
use Evervault\Tests\e2e\EndToEndTestCase;

class FunctionTest extends EndToEndTestCase {

    public function testFunctionRun() {
        $encryptedName = self::$evervaultClient->encrypt('John Doe');
        $encryptedAge = self::$evervaultClient->encrypt(42);
        $encryptedIsAlive = self::$evervaultClient->encrypt(true);
        $response = self::$evervaultClient->run('go-function-synthetic', [
            'name' => $encryptedName,
            'age' => $encryptedAge,
            'isAlive' => $encryptedIsAlive
        ]);
        $this->assertEquals($response->message, 'OK');
    }

    public function testFunctionRunAsync() {
        $encryptedName = self::$evervaultClient->encrypt('John Doe');
        $encryptedAge = self::$evervaultClient->encrypt(42);
        $encryptedIsAlive = self::$evervaultClient->encrypt(true);
        self::$evervaultClient->run('go-function-synthetic', [
            'name' => $encryptedName,
            'age' => $encryptedAge,
            'isAlive' => $encryptedIsAlive
        ], [
            'async' => true
        ]);
        $this->assertTrue(true);
    }

    public function testCreateFunctionRunToken() {
        $encryptedName = self::$evervaultClient->encrypt('John Doe');
        $encryptedAge = self::$evervaultClient->encrypt(42);
        $encryptedIsAlive = self::$evervaultClient->encrypt(true);
        $response = self::$evervaultClient->createRunToken('go-function-synthetic', [
            'name' => $encryptedName,
            'age' => $encryptedAge,
            'isAlive' => $encryptedIsAlive
        ]);
        $this->assertNotEmpty($response->token);
    }
}