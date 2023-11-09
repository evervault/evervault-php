<?php

namespace Evervault;

class FunctionRun {
    private $id;
    private $result;

    function __construct($id, $result) {
        $this->id = $id;
        $this->result = $result;
    }

    public function getId() {
        return $this->id;
    }

    public function getResult() {
        return $this->result;
    }
}