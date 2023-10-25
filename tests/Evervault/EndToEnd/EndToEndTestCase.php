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

        // app_3af8435b1a34
        // ev:key:1:WVcJIsw73WamND0xdgynqoi7p4gR6UBItj0nTrJc5OwtUMNnikcIxSEQgTpGD3Tf:qG32Jo:/lg+bP
        self::$evervaultClient = new Evervault("app_3af8435b1a34", "ev:key:1:WVcJIsw73WamND0xdgynqoi7p4gR6UBItj0nTrJc5OwtUMNnikcIxSEQgTpGD3Tf:qG32Jo:/lg+bP");
    }
}