<?php

namespace Evervault\Tests\e2e;

class ClientSideTokenTest extends EndToEndTestCase {

    public function testCreateClientSideDecryptToken()
    {
        $array = [
            'name' => 'John Doe',
            'age' => 42,
            'isAlive' => true
        ];
        $encrypted = self::$evervaultClient->encrypt($array);
        $response = self::$evervaultClient->createClientSideDecryptToken($encrypted);
        $this->assertNotEmpty($response->id);
        $this->assertNotEmpty($response->token);

        $decrypted = $this->decrypt($response->token, $encrypted);
        $this->assertEquals($array, $decrypted);
    }

    public function testCreateClientSideDecryptTokenWithExpiry()
    {
        $array = [
            'name' => 'John Doe',
            'age' => 42,
            'isAlive' => true
        ];
        $encrypted = self::$evervaultClient->encrypt($array);
        $expiry = time() + 5*60;
        $response = self::$evervaultClient->createClientSideDecryptToken($encrypted, $expiry);
        $this->assertNotEmpty($response->id);
        $this->assertNotEmpty($response->token);
        $this->assertEquals($expiry * 1000, $response->expiry);

        $decrypted = $this->decrypt($response->token, $encrypted);
        $this->assertEquals($array, $decrypted);

    }

    private function decrypt($token, $payload) {
        $url = "https://api.evervault.com/decrypt";

        $ch = curl_init($url);
        $headers = [
            "authorization: Token $token",
            'content-type: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if ($response === false) {
            echo "Curl Error: " . curl_error($ch);
        } else {
            echo $response;
        }
        curl_close($ch);
    }

}