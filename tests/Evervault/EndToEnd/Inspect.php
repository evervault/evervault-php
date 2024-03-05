<?php

namespace Evervault\Tests\EndToEnd;

class InspectTest extends EndToEndTestCase {

    public function testInspectCardNumber()
    {
        $value = "4242424242424242";
        $encrypted = self::$evervaultClient->encrypt($value, 'payments-data');
        $inspected = self::$evervaultClient->inspect($encrypted);
        $this->assertEquals("string", $inspected->getType());
        $this->assertEquals("payments-data", $inspected->getRole());
        $this->assertEquals("42424242", $inspected->getBin());
        $this->assertEquals("4242", $inspected->getLastFour());
        $this->assertEquals("visa", $inspected->getBrand());
        $this->assertEquals("credit", $inspected->getFunding());
        $this->assertEquals("consumer", $inspected->getSegment());
        $this->assertEquals("uk", $inspected->getCountry());
        $this->assertEquals("gbp", $inspected->getCurrency());
        $this->assertEquals("Stripe Uk limited", $inspected->getIssuer());
    }

    public function testInspectGenericValue()
    {
        $value = 'hello world!';
        $encrypted = self::$evervaultClient->encrypt($value, 'secret-message');
        $inspected = self::$evervaultClient->inspect($encrypted);
        $this->assertEquals("string", $inspected->getType());
        $this->assertEquals("payments-data", $inspected->getRole());
    }


}