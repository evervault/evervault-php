<?php

use PHPUnit\Framework\TestCase;
use Evervault\Evervault;
use Evervault\Tests\e2e\EndToEndTestCase;

class OutboundRelayTest extends EndToEndTestCase {

    public function testEnableOutboundRelay() 
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://o54dbmzbcj.execute-api.us-east-2.amazonaws.com/production?uuid=token&mode=outbound');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "string" => self::$evervaultClient->encrypt("some_string"),
            "number" => self::$evervaultClient->encrypt(1234567890),
            "boolean" => self::$evervaultClient->encrypt(true),
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);

        self::$evervaultClient->enableOutboundRelay($ch);

        $response = json_decode(curl_exec($ch), true);

        $this->assertEquals($response['request']['string'], false);
        $this->assertEquals($response['request']['number'], false);
        $this->assertEquals($response['request']['boolean'], false);
        
        curl_close($ch);
    }


}