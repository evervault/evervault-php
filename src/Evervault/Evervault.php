<?php

namespace Evervault;

class Evervault {
    public $cryptoClient;
    public $httpClient;
    public $configClient;

    const VERSION = '0.0.1';

    function __construct($apiKey, $options = []) {
        // Check if API key is valid
        $this->configClient = new EvervaultConfig();
        $this->httpClient = new EvervaultHttp(
            $apiKey,
            $this->configClient->getApiBaseUrl(), 
            $this->configClient->getCageRunBaseUrl()
        );
        $this->cryptoClient = new EvervaultCrypto(
            $this->httpClient->getCageKey()
        );
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

    public function run($cageName, $cageData) {
        return $this->httpClient->runCage($cageName, $cageData);
    }
}