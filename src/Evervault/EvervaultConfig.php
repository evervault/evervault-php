<?php

namespace Evervault;

class EvervaultConfig {
    public $apiBaseUrl;
    public $functionRunBaseUrl;

    function __construct() {
        $this->apiBaseUrl = getenv('EV_API_URL') ? getenv('EV_API_URL') : 'https://api.evervault.com';
        $this->functionRunBaseUrl = getenv('EV_CAGE_RUN_URL') ? getenv('EV_CAGE_RUN_URL') : 'https://run.evervault.com';
    }

    public function getApiBaseUrl() {
        return $this->apiBaseUrl;
    }
    
    public function getFunctionRunBaseUrl() {
        return $this->functionRunBaseUrl;
    }
}
