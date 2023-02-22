<?php

namespace Evervault;

class EvervaultHttp {
    private static $apiKey;
    private static $apiBaseUrl;
    public static $functionRunBaseUrl;

    private static $curl;

    private $appKeyPath = '/cages/key';
    
    private $appKey;

    function __construct($apiKey, $apiBaseUrl, $functionRunBaseUrl) {
        self::$apiKey = $apiKey;
        self::$apiBaseUrl = $apiBaseUrl;
        self::$functionRunBaseUrl = $functionRunBaseUrl;

        self::$curl = curl_init();
    }

    private function _getDefaultHeaders() {
        return [
            'api-key: '.self::$apiKey,
            'content-type: application/json',
            'accept: application/json',
            'user-agent: evervault-php/'.Evervault::VERSION
        ];
    }

    private function _buildApiUrl($path) {
        return self::$apiBaseUrl . $path;
    }

    private function _buildFunctionUrl($functionName) {
        return self::$functionRunBaseUrl . '/' . $functionName;
    }

    public function getAppEcdhKey() {
        return $this->_makeApiRequest(
            'GET',
            $this->appKeyPath,
        )->ecdhP256Key;
    }

    private function _handleApiResponse($curl, $response, $headers = []) {
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $requestedUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

        if ($responseCode === 401 and strncmp($requestedUrl, 'https://run.evervault.com/', strlen('https://run.evervault.com')) === 0) {
            throw new EvervaultError('Your function could not be found. Please ensure you have deployed a function with the name you provided.');
        } else if ($responseCode === 401) {
            throw new EvervaultError('Your API key was invalid. Please verify it matches your API key in the Evervault Dashboard.');
        } else if ($responseCode !== 200) {
            throw new EvervaultError('There was an error initializing the Evervault SDK. Please try again or contact support@evervault.com for help.');
        } else {
            return json_decode($response);
        }
    }

    private function _makeApiRequest($method, $path, $body = [], $headers = []) {
        curl_setopt(
            self::$curl, 
            CURLOPT_URL, 
            $this->_buildApiUrl($path)
        );

        curl_setopt(
            self::$curl,
            CURLOPT_HTTPHEADER,
            array_merge(
                $this->_getDefaultHeaders(), 
                $headers
            )
        );

        if (strtolower($method) === 'post') {
            curl_setopt(
                self::$curl,
                CURLOPT_POSTFIELDS,
                json_encode($body, JSON_FORCE_OBJECT)
            );
        }

        curl_setopt(
            self::$curl,
            CURLOPT_RETURNTRANSFER,
            true
        );

        $response = curl_exec(self::$curl);
        return $this->_handleApiResponse(self::$curl, $response, $headers);
    }

    private function _makefunctionRunRequest($functionName, $body = [], $headers = []) {
        curl_setopt(
            self::$curl, 
            CURLOPT_URL, 
            $this->_buildfunctionUrl($functionName)
        );

        curl_setopt(
            self::$curl,
            CURLOPT_HTTPHEADER,
            array_merge(
                $this->_getDefaultHeaders(), 
                $headers
            )
        );

        curl_setopt(
            self::$curl,
            CURLOPT_POSTFIELDS,
            json_encode($body, JSON_FORCE_OBJECT)
        );

        curl_setopt(
            self::$curl,
            CURLOPT_RETURNTRANSFER,
            true
        );

        $response = curl_exec(self::$curl);

        return $this->_handleApiResponse(self::$curl, $response, $headers);
    }

    public function runfunction($functionName, $functionData, $additionalHeaders) {
        $response = $this->_makefunctionRunRequest($functionName, $functionData, $additionalHeaders);

        if (in_array('x-async: true', $additionalHeaders)) {
            return $response;
        } else {
            return $response->result;
        }
    }
}