<?php

namespace Evervault;

class EncryptedCardNumberMetadata extends EncryptedStringMetadata {
    private $bin;
    private $lastFour;
    private $brand;
    private $funding;
    private $country;
    private $currency;
    private $issuer;

    public function __construct($type, $role, $encryptedAt, $fingerprint, $bin, $lastFour, $brand, $funding, $country, $currency, $issuer) {
        parent::__construct($type, $role, $encryptedAt, $fingerprint);
        $this->bin = $bin;
        $this->lastFour = $lastFour;
        $this->brand = $brand;
        $this->funding = $funding;
        $this->country = $country;
        $this->currency = $currency;
        $this->issuer = $issuer;
    }

    public static function fromApiResponse($response) {
        return new EncryptedCardNumberMetadata(
            $response['type'], 
            $response['role'], 
            $response['encryptedAt'], 
            $response['fingerprint'], 
            $response['metadata']['bin'], 
            $response['metadata']['lastFour'], 
            $response['metadata']['brand'], 
            $response['metadata']['funding'], 
            $response['metadata']['country'], 
            $response['metadata']['currency'], 
            $response['metadata']['issuer']
        );
    }

    public function getBin() {
        return $this->bin;
    }

    public function getLastFour() {
        return $this->lastFour;
    }

    public function getBrand() {
        return $this->brand;
    }

    public function getFunding() {
        return $this->funding;
    }

    public function getCountry() {
        return $this->country;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function getIssuer() {
        return $this->issuer;
    }

}