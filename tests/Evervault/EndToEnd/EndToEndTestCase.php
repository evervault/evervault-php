<?php

namespace Evervault\Tests\EndToEnd;

use PHPUnit\Framework\TestCase;
use Evervault\Evervault;

class EndToEndTestCase extends TestCase {
    protected static $evervaultClient;
    
    public static function setUpBeforeClass(): void
    {
        // $appId = getenv('TEST_EV_APP_ID');
        // $apiKey = getenv('TEST_EV_API_KEY');
        // self::$evervaultClient = new Evervault($appId, $apiKey);
        
        $apiKey = "ev:key:1:7nQ7joJNcLWO2GpWiNPCKmF2fq7hdzEIJIYnyDQ0g8KU0uCEovkMxmCzU5TZwYlxO:VZoLcg:FN997q";
        $appId = "app_eead1d640d7c";
        self::$evervaultClient = new Evervault($appId, $apiKey);
    }
}