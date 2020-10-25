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
            'user-agent: evervault-php/0.0.1',
            'x-ninja-mode: yes'
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

        return json_decode(
            curl_exec($this->curl)
        );
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

        return json_decode(
            curl_exec($this->curl)
        );
    }

    public function runCage($cageName, $cageData) {
        return $this->_makeCageRunRequest($cageName, [], $cageData)->result;
    }
}