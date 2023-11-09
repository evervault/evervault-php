<?php

namespace Evervault\Exception;

class FunctionTimeoutException extends EvervaultException {
    public function __construct($message) {
        parent::__construct($message);
    }
}
