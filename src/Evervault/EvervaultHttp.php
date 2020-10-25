<?php

namespace Evervault;

class EvervaultHttp {
    private static $apiKey;
    private static $apiBaseUrl;
    public static $cageRunBaseUrl;

    private static $curl;

    private $cageKeyPath = '/cages/key';
    
    private $cageKey;

    function __construct($apiKey, $apiBaseUrl, $cageRunBaseUrl) {
        $this->apiKey = $apiKey;
        $this->apiBaseUrl = $apiBaseUrl;
        $this->cageRunBaseUrl = $cageRunBaseUrl;

        $this->curl = curl_init();
    }

    private function _getDefaultHeaders() {
        return [
            'api-key: '.$this->apiKey,
            'content-type: application/json',
            'accept: application/json',
            'user-agent: evervault-php/0.0.1'
        ];
    }

    private function _buildApiUrl($path) {
        return $this->apiBaseUrl . $path;
    }

    private function _buildCageUrl($cageName) {
        return $this->cageRunBaseUrl . '/' . $cageName;
    }

    public function getCageKey() {
        return $this->_makeApiRequest(
            'GET',
            $this->cageKeyPath,
        )->key;
    }

    private function _handleApiResponse($curl, $response) {
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $requestedUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

        if ($responseCode === 401 and strncmp($requestedUrl, 'https://cage.run/', strlen('https://cage.run')) === 0) {
            throw new EvervaultError('Your Cage could not be found. Please ensure you have deployed a Cage with the name your provided.');
        } else if ($responseCode === 401) {
            throw new EvervaultError('Your API key was invalid. Please verify it matches your API key in the Evervault Dashboard.');
        } else if ($responseCode !== 200) {
            throw new EvervaultError('There was an error initializing the Evervault SDK. Please try again or contact support@evervault.com for help.');
        } else {
            return json_decode($response);
        }
    }

    private function _makeApiRequest($method, $path, $headers = [], $body = []) {
        curl_setopt(
            $this->curl, 
            CURLOPT_URL, 
            $this->_buildApiUrl($path)
        );

        curl_setopt(
            $this->curl,
            CURLOPT_HTTPHEADER,
            array_merge(
                $this->_getDefaultHeaders(), 
                $headers
            )
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

    private function _makeCageRunRequest($cageName, $headers = [], $body = []) {
        curl_setopt(
            $this->curl, 
            CURLOPT_URL, 
            $this->_buildCageUrl($cageName)
        );

        curl_setopt(
            $this->curl,
            CURLOPT_HTTPHEADER,
            array_merge(
                $this->_getDefaultHeaders(), 
                $headers
            )
        );

        curl_setopt(
            $this->curl,
            CURLOPT_POSTFIELDS,
            json_encode($body, JSON_FORCE_OBJECT)
        );

        curl_setopt(
            $this->curl,
            CURLOPT_RETURNTRANSFER,
            true
        );

        $response = curl_exec($this->curl);

        return $this->_handleApiResponse($this->curl, $response);
    }

    public function runCage($cageName, $cageData) {
        return $this->_makeCageRunRequest($cageName, [], $cageData)->result;
    }
}