<?php

namespace Evervault;

class EvervaultConfig {
    public $apiBaseUrl = 'https://api.evervault.com';
    public $functionRunBaseUrl = 'https://run.evervault.com';

    public function getApiBaseUrl() {
        return $this->apiBaseUrl;
    }
    
    public function getFunctionRunBaseUrl() {
        return $this->functionRunBaseUrl;
    }
}