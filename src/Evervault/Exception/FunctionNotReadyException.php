<?php

namespace Evervault\Exception;

class FunctionNotReadyException extends EvervaultException {
    public function __construct($message) {
        parent::__construct($message);
    }
}
