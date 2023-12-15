<?php

namespace Evervault\Tests\EndToEnd;

use Evervault\Tests\EndToEnd\EndToEndTestCase;

class ClientSideTokenTest extends EndToEndTestCase {

    public function testCreateClientSideDecryptToken()
    {
        $data = [
            "string" => "apple",
            "number" => 12345,
            "double" => 123.45,
            "true" => true,
            "false" => false
        ];
        $encrypted = self::$evervaultClient->encrypt($data);
        $token = self::$evervaultClient->createClientSideDecryptToken($encrypted);

        $decrypted = $this->decrypt($token->token, $encrypted);
        $this->assertEquals($data, $decrypted);
    }

    public function testCreateClientSideDecryptTokenWithExpiry()
    {
        $data = [
            "string" => "apple",
            "number" => 12345,
            "double" => 123.45,
            "true" => true,
            "false" => false
        ];
        $encrypted = self::$evervaultClient->encrypt($data);
        $expiry = time() + 5*60;
        $token = self::$evervaultClient->createClientSideDecryptToken($encrypted, $expiry);

        $decrypted = $this->decrypt($token->token, $encrypted);
        $this->assertEquals($data, $decrypted);
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
        curl_close($ch);
        return json_decode($response, true);
    }

}