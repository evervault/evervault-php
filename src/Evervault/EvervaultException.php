<?php

namespace Evervault;

class EvervaultException extends \Exception {
    public function __construct($message, \Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }
}

class FunctionNotFoundException extends EvervaultException {
    public function __construct($message) {
        parent::__construct($message);
    }
}

class FunctionTimeoutException extends EvervaultException {
    public function __construct($message) {
        parent::__construct($message);
    }
}

class FunctionNotReadyException extends EvervaultException {
    public function __construct($message) {
        parent::__construct($message);
    }
}

class FunctionRunException extends EvervaultException {
    private $runId;
    private $stackTrace;

    public function __construct($message, $runId, $stackTrace) {
        parent::__construct($message);
        $this->runId = $runId;
        $this->stackTrace = $stackTrace;
    }

    public function getRunId() {
        return $this->runId;
    }

    public function getStackTrace() {
        return $this->stackTrace;
    }
}

class FunctionInitializationException extends FunctionRunException {
    public function __construct($message, $runId, $stackTrace) {
        parent::__construct($message, $runId, $stackTrace);
    }
}

class FunctionRuntimeException extends FunctionRunException {
    public function __construct($message, $runId, $stackTrace) {
        parent::__construct($message, $runId, $stackTrace);
    }
}

class ForbiddenIpException extends EvervaultException {
    public function __construct($message) {
        parent::__construct($message);
    }
}