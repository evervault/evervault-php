<?php

namespace Evervault\Tests\EndToEnd;

use Evervault\Tests\EndToEnd\EndToEndTestCase;

class OutboundRelayTest extends EndToEndTestCase {

    public function testEnableOutboundRelay() 
    {
        $data = [
            "string" => "apple",
            "number" => 12345,
            "double" => 123.45,
            "true" => true,
            "false" => false
        ];
        $encrypted = self::$evervaultClient->encrypt($data, "permit-all");

        // Request outside Outbound Destination
        $response = $this->makeRequest($this->syntheticEndpointUrl('php-sdk-test'), $encrypted, false);

        $this->assertEquals($response['request']['string'], true);
        $this->assertEquals($response['request']['number'], true);
        $this->assertEquals($response['request']['double'], true);
        $this->assertEquals($response['request']['true'], true);
        $this->assertEquals($response['request']['false'], true);

        // Request to Outbound Destination
        $response = $this->makeRequest($this->syntheticEndpointUrl('php-sdk-test'), $encrypted, true);

        $this->assertEquals($response['request']['string'], false);
        $this->assertEquals($response['request']['number'], false);
        $this->assertEquals($response['request']['double'], false);
        $this->assertEquals($response['request']['true'], false);
        $this->assertEquals($response['request']['false'], false);        
    }

    private function syntheticEndpointUrl($syntheticUuid)
    {
        $baseUrl = getenv('EV_SYNTHETIC_ENDPOINT_URL');

        if (!$baseUrl) {
            $this->fail('EV_SYNTHETIC_ENDPOINT_URL is not set.');
        }

        // Both parameters are required; the endpoint responds 502 if either is missing.
        return $baseUrl . '?' . http_build_query([
            'syntheticUuid' => $syntheticUuid,
            'mode' => 'outbound',
        ]);
    }

    private function makeRequest($url, $payload, $enableOutboundRelay)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        if ($enableOutboundRelay) {
          self::$evervaultClient->enableOutboundRelay($ch);
        }
        $response = json_decode(curl_exec($ch), true);
        return $response;
    }
}
