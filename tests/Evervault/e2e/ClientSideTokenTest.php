<?php

namespace Evervault\Tests\e2e;

class ClientSideTokenTest extends EndToEndTestCase {

    public function testCreateClientSideDecryptToken()
    {
        $encryptedName = self::$evervaultClient->encrypt('John Doe');
        $encryptedAge = self::$evervaultClient->encrypt(42);
        $encryptedIsAlive = self::$evervaultClient->encrypt(true);
        $response = self::$evervaultClient->createClientSideDecryptToken([
            'name' => $encryptedName,
            'age' => $encryptedAge,
            'isAlive' => $encryptedIsAlive
        ]);
        $this->assertNotEmpty($response->id);
        $this->assertNotEmpty($response->token);
    }

}