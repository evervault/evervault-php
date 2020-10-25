<?php

namespace Evervault;

class EvervaultError extends \Exception {
    public function __construct($message, $code = 0, Exception $previous = null) {
        // TODO: send error telemetry data to Evervault
        parent::__construct($message, $code, $previous);
    }
}