<?php

namespace Evervault;

class EvervaultCrypto {
    public $cageKey;

    public $cipher = 'aes-256-gcm';

    public $keyLength = 32;
    public $authTagLength = 16;
    public $ivLength = 16;

    public $header = [
        'iss' => 'evervault',
        'version' => 1
    ];

    function __construct($cageKey) {
        $this->cageKey = $cageKey;
    }

    private function _generateBytes($length = 32) {
        return openssl_random_pseudo_bytes($length);
    }

    private function _extractPublicKey($key) {
        return openssl_pkey_get_public(
            "-----BEGIN PUBLIC KEY-----\n"
            .chunk_split($this->cageKey, 64, "\n")
            ."-----END PUBLIC KEY-----"
        );
    }

    private function _format($datatype = 'string', $encryptedKey, $keyIv, $encryptedData) {
        $header = EvervaultUtils::base64url_encode(
            json_encode(
                array_merge(
                    $this->header,
                    ['datatype' => $datatype]
                ),
                JSON_FORCE_OBJECT
            )
        );


        $payload = EvervaultUtils::base64url_encode(
            json_encode(
                [
                    'cageData' => $encryptedKey,
                    'keyIv' => $keyIv,
                    'sharedEncryptedData' => $encryptedData
                ],
                JSON_FORCE_OBJECT
            )
        );

        $uuid = EvervaultUtils::uuidv4();

        return $header . '.' . $payload . '.' . $uuid;
    }

    private function _publicEncrypt($data) {
        openssl_public_encrypt(
            $data,
            $encrypted,
            $this->_extractPublicKey($this->cageKey),
            OPENSSL_PKCS1_OAEP_PADDING
        );

        return base64_encode($encrypted);
    }

    private function _encryptArray($array) {
        array_walk_recursive($array, function (&$value) {
            $value = $this->_encryptString($value);
        });

        return $array;
    }

    private function _encryptString($string) {
        if (in_array($this->cipher, openssl_get_cipher_methods())) {
            $aesKey = $this->_generateBytes($this->keyLength);
            $keyIv = $this->_generateBytes($this->ivLength);
            
            $ciphertext = openssl_encrypt(
                (string) $string,
                $this->cipher,
                $aesKey,
                OPENSSL_RAW_DATA,
                $keyIv,
                $tag,
                '',
                $this->authTagLength
            );

            $ciphertext = base64_encode($ciphertext.$tag);

            $encryptedAesKey = $this->_publicEncrypt($aesKey);

            $datatype = is_numeric($string) ? 'number' : 'string';

            return $this->_format($datatype, $encryptedAesKey, base64_encode($keyIv), $ciphertext);
        } else {
            throw new EvervaultError('AES-256-GCM is not supported. Please upgrade to PHP >7.1.');
        }
        return 'encrypted'.$string;
    }

    public function encryptData($data) {
        if (is_array($data)) {
            return $this->_encryptArray($data);
        }

        if (is_string($data) || is_numeric($data)) {
            return $this->_encryptString($data);
        }

        throw new EvervaultError('Data is not encryptable');
    }
}