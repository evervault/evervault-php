<?php

namespace Evervault;

class EncryptedStringMetadata {
    private $type;
    private $role;
    private $encryptedAt;
    private $fingerprint;

    public function __construct($type, $role, $encryptedAt, $fingerprint) {
        $this->type = $type;
        $this->role = $role;
        $this->encryptedAt = $encryptedAt;
        $this->fingerprint = $fingerprint;
    }

    public static function fromApiResponse($response) {
        return new EncryptedStringMetadata(
            $response['type'], 
            $response['role'], 
            $response['encryptedAt'], 
            $response['fingerprint']
        );
    }

    public function getType() {
        return $this->type;
    }

    public function getRole() {
        return $this->role;
    }

    public function getEncryptedAt() {
        return $this->encryptedAt;
    }
    
    public function getFingerprint() {
        return $this->fingerprint;
    }
}