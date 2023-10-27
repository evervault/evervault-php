<?php

namespace Evervault;

use Evervault\Exception\EvervaultException;

class Evervault {    
    private $cryptoClient;
    private $httpClient;
    private $configClient;
    private $outboundRelayCaPath;
    private $relayAuthString;
    private $appKeys;
    private $apiKey;
    private $appUuid;

    private $outboundRelayUrl;
    private $outboundRelayCaUrl;
    private $outboundRelayDestinations;

    private $caFilename = '/evervault-ca.pem';

    function __construct($appId, $apiKey, $options = []) {
        $this->appUuid = $appId;
        $this->apiKey = $apiKey;

        EvervaultUtils::validateAppUuidAndApiKey($this->appUuid, $this->apiKey);

        $this->outboundRelayUrl = getenv('EV_TUNNEL_HOSTNAME') ? getenv('EV_TUNNEL_HOSTNAME') : 'https://relay.evervault.com:443';
        $this->outboundRelayCaUrl = getenv('EV_CERT_HOSTNAME') ? getenv('EV_CERT_HOSTNAME') : 'https://ca.evervault.com';
        $this->configClient = new EvervaultConfig();
        $this->httpClient = new EvervaultHttp(
            $apiKey,
            $appId,
            $this->configClient->getApiBaseUrl(), 
            $this->configClient->getFunctionRunBaseUrl()
        );

        // If CA doesn't exist, or if CA is >1 minute old then refresh
        if (!file_exists(sys_get_temp_dir() . $this->caFilename) 
        || (file_exists(sys_get_temp_dir() . $this->caFilename) 
            && (time() - filemtime(sys_get_temp_dir() . $this->caFilename)) > (1 * 60))
            ) {
            $outboundRelayCaFile = fopen(sys_get_temp_dir() . $this->caFilename, "w");
            fwrite($outboundRelayCaFile, file_get_contents($this->outboundRelayCaUrl));
        }

        $this->outboundRelayCaPath = sys_get_temp_dir() . $this->caFilename;
    }

    private function _createCryptoClientIfNotExists() {
        if (!$this->cryptoClient) {
            if (!$this->appKeys) {
                $this->appKeys = $this->httpClient->getAppEcdhKey();
            }
            $this->cryptoClient = new EvervaultCrypto(
                $this->appKeys->appEcdhP256Key
            );
        }
    }

    public function encrypt($data, $role = null) {
        $this->_createCryptoClientIfNotExists();

        if (!isset($data) || $data === "") {
            throw new EvervaultException('No data provided: `encrypt()` must be called with a non-empty string, number, boolean, or array.');
        }

        if (!(is_bool($data) || is_string($data) || is_array($data) || is_numeric($data))) {
            throw new EvervaultException('Invalid data type for encryption. Please ensure the input is of type string, number, boolean, or array.');
        }

        return $this->cryptoClient->encryptData($data, $role);
    }

    public function decrypt($data) {
        if (!$data || (!is_string($data) && !is_array($data))) {
            throw new EvervaultException('`decrypt()` must be called with a non-empty string or array.');
        }
        return $this->httpClient->decrypt($data);
    }

    public function run($functionName, $functionPayload) {
        return $this->httpClient->runFunction($functionName, $functionPayload);
    }

    public function enableOutboundRelay($curlHandler) {
        $this->_createCryptoClientIfNotExists();

        if (!$this->relayAuthString) {
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

    public function createRunToken($functionName, $payload = []) {
        $response = $this->httpClient->createRunToken($functionName, $payload);
        return new EvervaultToken($response['token']);
    }

    public function createClientSideDecryptToken($data, $expiry = null) {
        if (!$data) {
            throw new EvervaultException('The `$data` parameter is required and ensures the issued token can only be used to decrypt that specific payload.');
        }

        if ($expiry) {
            $expiry = $expiry * 1000;
        }
        
        $response = $this->httpClient->createToken("api:decrypt", $data, $expiry);
        return new EvervaultToken($response['token']);
    }
}
