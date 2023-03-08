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
    private $appKeys;
    private $apiKey;

    private $outboundRelayUrl;
    private $outboundRelayCaUrl;
    private $outboundRelayDestinations;

    function __construct($apiKey, $options = []) {
        $this->apiKey = $apiKey;
        $this->outboundRelayUrl = getenv('EV_TUNNEL_HOSTNAME') ? getenv('EV_TUNNEL_HOSTNAME') : 'https://relay.evervault.com:443';
        $this->outboundRelayCaUrl = getenv('EV_CERT_HOSTNAME') ? getenv('EV_CERT_HOSTNAME') : 'https://ca.evervault.com';
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
        $this->outboundRelayCaFile = tmpfile();
        fwrite($this->outboundRelayCaFile, file_get_contents($this->outboundRelayCaUrl));
        $this->outboundRelayCaPath = stream_get_meta_data($this->outboundRelayCaFile)['uri'];
    }

    public function encrypt($data) {
        if (!$this->cryptoClient) {
            if (!$this->appKeys) {
                $this->appKeys = $this->httpClient->getAppEcdhKey();
            }
            $this->cryptoClient = new EvervaultCrypto(
                $this->appKeys->appEcdhP256Key
            );
        }
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
        if (!$this->relayAuthString) {
            if (!$this->appKeys) {
                $this->appKeys = $this->httpClient->getAppEcdhKey();
            }
            $this->relayAuthString = $this->appKeys->appId . ':' . $this->apiKey;
        }

        if (!$this->outboundRelayDestinations) {
            $this->outboundRelayDestinations = $this->httpClient->getAppRelayConfiguration();
        }

        $requestUrl = curl_getinfo($curlHandler, CURLINFO_EFFECTIVE_URL);

        if (EvervaultUtils::isDecryptionDomain($requestUrl, $this->outboundRelayDestinations)) {
            curl_setopt($curlHandler, CURLOPT_PROXY, $this->outboundRelayUrl);
            curl_setopt($curlHandler, CURLOPT_PROXYUSERPWD, $this->relayAuthString);
            curl_setopt($curlHandler, CURLOPT_CAINFO, $this->outboundRelayCaPath);
        }
    }

    public function createRunToken($functionName, $payload) {
        return $this->httpClient->createRunToken($functionName, $payload);
    }
}
