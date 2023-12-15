<?php

namespace Evervault\Exception;

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