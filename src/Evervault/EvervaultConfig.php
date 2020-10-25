<?php

namespace Evervault;

class EvervaultConfig {
    public $apiBaseUrl = 'https://api.evervault.com';
    public $cageRunBaseUrl = 'https://cage.run';

    public function getApiBaseUrl() {
        return $this->apiBaseUrl;
    }
    
    public function getCageRunBaseUrl() {
        return $this->cageRunBaseUrl;
    }
}