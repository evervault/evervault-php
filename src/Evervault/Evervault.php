<?php

namespace Evervault;

class Evervault {
    public $cryptoClient;
    public $httpClient;
    public $configClient;

    const VERSION = '0.0.5';

    function __construct($apiKey, $options = []) {
        // Check if API key is valid
        $this->configClient = new EvervaultConfig();
        $this->httpClient = new EvervaultHttp(
            $apiKey,
            $this->configClient->getApiBaseUrl(), 
            $this->configClient->getFunctionRunBaseUrl()
        );
        $this->cryptoClient = new EvervaultCrypto(
            $this->httpClient->getAppEcdhKey()
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
}
