<?php

namespace Evervault;

class Evervault {
    const VERSION = '0.0.5';
    
    public $cryptoClient;
    public $httpClient;
    public $configClient;
    public $outboundRelayCaFile;
    public $outboundRelayCaPath;
    private $relayAuthString;

    private $outboundRelayUrl = 'https://relay.evervault.com:443';
    private $outboundRelayCaUrl = 'https://ca.evervault.com';

    function __construct($apiKey, $options = []) {
        // Check if API key is valid
        $this->configClient = new EvervaultConfig();
        $this->httpClient = new EvervaultHttp(
            $apiKey,
            $this->configClient->getApiBaseUrl(), 
            $this->configClient->getFunctionRunBaseUrl()
        );
        $appKeys = $this->httpClient->getAppEcdhKey();
        $this->cryptoClient = new EvervaultCrypto(
            $appKeys->appEcdhP256Key
        );
        $this->relayAuthString = $appKeys->appId . ':' . $apiKey;
        $this->outboundRelayCaFile = tmpfile();
        fwrite($this->outboundRelayCaFile, file_get_contents($this->outboundRelayCaUrl));
        $this->outboundRelayCaPath = stream_get_meta_data($this->outboundRelayCaFile)['uri'];
    }

    public function encrypt($data) {
        if (!$data) {
            throw new EvervaultError('Please provide some data to encrypt.');
        }

        if (!(is_string($data) or is_array($data) or is_numeric($data))) {
            throw new EvervaultError('The data to encrypt must be a string, number or object.');
        }

        return $this->cryptoClient->encryptData($data);
    }

    public function run($functionName, $functionData, $options = ['version' => 0, 'async' => false]) {
        $additionalHeaders = [];

        if (!is_null($options['version'])) {
            if (!is_numeric($options['version'])) {
                throw new EvervaultError('Function version must be a number');
            } else {
                $additionalHeaders[] = 'x-version-id: ' . $options['version'];
            }
        }

        if ($options['async']) {
            $additionalHeaders[] = 'x-async: true';
        }

        return $this->httpClient->runFunction($functionName, $functionData, $additionalHeaders);
    }

    public function enableOutboundRelay($curlHandler) {
        curl_setopt($curlHandler, CURLOPT_PROXY, $this->outboundRelayUrl);
        curl_setopt($curlHandler, CURLOPT_PROXYUSERPWD, $this->relayAuthString);
        curl_setopt($curlHandler, CURLOPT_CAINFO, $this->outboundRelayCaPath);
    }

    public function createRunToken($functionName, $payload) {
        return $this->httpClient->createRunToken($functionName, $payload);
    }
}
