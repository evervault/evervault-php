<?php

namespace Evervault\Tests\EndToEnd;

use PHPUnit\Framework\TestCase;
use Evervault\Evervault;

class EndToEndTestCase extends TestCase {
    protected static $evervaultClient;
    
    public static function setUpBeforeClass(): void
    {
        $appId = getenv('TEST_EV_APP_ID');
        $apiKey = getenv('TEST_EV_API_KEY');
        // self::$evervaultClient = new Evervault($appId, $apiKey);

        self::$evervaultClient = new Evervault('app_3af8435b1a34', 'ev:key:1:5CRBXDdwZXrEfgfNrz9PZN47lb4W8batmcOvaTXMW8hxaNXCWyvVyhxBKKbndBUXz:qG32Jo:aZ+byX');
    }
}