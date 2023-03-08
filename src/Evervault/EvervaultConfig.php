<?php

namespace Evervault;

class EvervaultConfig {
    public $apiBaseUrl = 'https://api.evervault.com';
    public $functionRunBaseUrl = 'https://run.evervault.com';

    public function _construct() {
        $this->apiBaseUrl = getenv('EV_API_URL') || 'https://api.evervault.com';
        $thus->functionRunBaseUrl = getenv('EV_CAGE_RUN_URL') || 'https://run.evervault.com';
    }

    public function getApiBaseUrl() {
        return $this->apiBaseUrl;
    }
    
    public function getFunctionRunBaseUrl() {
        return $this->functionRunBaseUrl;
    }
}