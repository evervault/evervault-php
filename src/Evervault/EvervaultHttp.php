<?php

namespace Evervault;

use Evervault\Exception\EvervaultException;
use Evervault\Exception\FunctionNotReadyException;
use Evervault\Exception\FunctionTimeoutException;

class EvervaultHttp {
    private const APP_KEY_PATH = '/cages/key';
    private const RELAY_CONFIG_PATH = '/v2/relay-outbound';
    private const DECRYPT_PATH = '/decrypt';
    private const CREATE_TOKEN_PATH = '/client-side-tokens';
    private const FUNCTION_RUNS_PATH = '/functions/%s/runs';

    private $apiKey;
    private $appUuid;
    private $apiBaseUrl;

    private $curl;
    
    function __construct($apiKey, $appUuid, $apiBaseUrl) {
        $this->apiKey = $apiKey;
        $this->appUuid = $appUuid;
        $this->apiBaseUrl = $apiBaseUrl;

        $this->curl = curl_init();
    }

    private function _getDefaultHeaders($basicAuth = false) {
        $defaultHeaders = [
            'content-type: application/json',
            'accept: application/json',
            'user-agent: evervault-php/' . EvervaultVersion::VERSION,
        ];

        if ($basicAuth) {
            $defaultHeaders[] = 'authorization: Basic ' . base64_encode($this->appUuid . ':' . $this->apiKey);
        } else {
            $defaultHeaders[] = 'api-key: '.$this->apiKey;
        }

        return $defaultHeaders;
    }

    private function _buildApiUrl($path) {
        return $this->apiBaseUrl . $path;
    }

    public function getAppEcdhKey() {
        $appKeys = $this->_makeApiRequest(
            'GET',
            self::APP_KEY_PATH,
        );

        return (object) [
            "appEcdhP256Key" => $appKeys['ecdhP256Key'],
            "appId" => isset($appKeys['appUuid']) ? $appKeys['appUuid'] : $appKeys['teamUuid']
        ];
    }

    public function getAppRelayConfiguration() {
        $relayConfig = $this->_makeApiRequest(
            'GET',
            self::RELAY_CONFIG_PATH,
        );
        return array_keys((array) $relayConfig['outboundDestinations']);
    }

    public function createRunToken($functionName, $payload = []) {
       return $this->_makeApiRequest(
            'POST',
            '/v2/functions/'.$functionName.'/run-token',
            $payload
        );
    }

    private function _handleApiResponse($curl, $response) {
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $json = json_decode($response, true);

        if ($responseCode >= 400) {
            if($json === null || empty($json['code']) || empty($json['detail'])) {
                throw new EvervaultException('An unexpected error occurred. If the problem persists, reach out to our Support Team at support@evervault.com.');
            }

            switch ($json['code']) {
                case 'functions/request-timeout':
                    throw new FunctionTimeoutException($json['detail']);
                    break;
                case 'functions/function-not-ready':
                    throw new FunctionNotReadyException($json['detail']);
                    break;
                default:
                    throw new EvervaultException($json['detail']);
            }
        } else {
            return $json;
        }
    }

    private function _makeApiRequest($method, $path, $body = [], $headers = [], $basicAuth = false) {
        curl_setopt(
            $this->curl, 
            CURLOPT_URL, 
            $this->_buildApiUrl($path)
        );

        curl_setopt(
            $this->curl,
            CURLOPT_HTTPHEADER,
            array_merge(
                $this->_getDefaultHeaders($basicAuth), 
                $headers
            )
        );

        curl_setopt(
            $this->curl,
            CURLOPT_HTTPGET,
            true
        );
        
        if (strtolower($method) === 'post') {
            curl_setopt(
                $this->curl,
                CURLOPT_POSTFIELDS,
                json_encode($body, JSON_FORCE_OBJECT)
            );
        }

        curl_setopt(
            $this->curl,
            CURLOPT_RETURNTRANSFER,
            true
        );

        $response = curl_exec($this->curl);
        return $this->_handleApiResponse($this->curl, $response);
    }

    public function runFunction($functionName, $functionPayload) {
        $payload = [
            'payload' => $functionPayload
        ];
        return $this->_makeApiRequest('POST', sprintf(self::FUNCTION_RUNS_PATH, $functionName), $payload, [], true);
    }

    public function decrypt($data) {
        $response = $this->_makeApiRequest('POST', self::DECRYPT_PATH, [
            'data' => $data
        ], [], true);
        return $response['data'];
    }

    public function createToken($action, $data, $expiry) {
        $payload = array(
            'action' => $action,
            'payload' => $data,
            'expiry' => $expiry
        );
        
        return $this->_makeApiRequest('POST', self::CREATE_TOKEN_PATH, $payload, [], true);
    }
}