<?php

namespace Evervault;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\Point\CompressedPointSerializer;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;
use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Util\NumberSize;

class EvervaultCrypto {
    public $appEcdhP256Key;
    public $decodedAppEcdhP256Key;

    public $cipher = 'aes-256-gcm';

    public $keyLength = 32;
    public $authTagLength = 16;
    public $ivLength = 12;

    public $header = [
        'iss' => 'evervault',
        'version' => 1
    ];

    public $P256ASN1Prefix = '3082014b3082010306072a8648ce3d02013081f7020101302c06072a8648ce3d0101022100ffffffff00000001000000000000000000000000ffffffffffffffffffffffff305b0420ffffffff00000001000000000000000000000000fffffffffffffffffffffffc04205ac635d8aa3a93e7b3ebbd55769886bc651d06b0cc53b0f63bce3c3e27d2604b031500c49d360886e704936a6678e1139d26b7819f7e900441046b17d1f2e12c4247f8bce6e563a440f277037d812deb33a0f4a13945d898c2964fe342e2fe1a7f9b8ee7eb4a7c0f9e162bce33576b315ececbb6406837bf51f5022100ffffffff00000000ffffffffffffffffbce6faada7179e84f3b9cac2fc632551020101034200';

    function __construct($appEcdhP256Key) {
        $this->appEcdhP256Key = $appEcdhP256Key;
        $this->decodedAppEcdhP256Key = base64_decode($appEcdhP256Key);
    }

    private function _generateBytes($length = 16) {
        return openssl_random_pseudo_bytes($length);
    }

    private function _format($datatype, $ephemeralEcdhPublicKey, $keyIv, $encryptedData) {
        return sprintf(
            "ev:Tk9D:%s%s:%s:%s:$", 
            $datatype, 
            EvervaultUtils::base64url_encode($keyIv), 
            EvervaultUtils::base64url_encode($ephemeralEcdhPublicKey), 
            EvervaultUtils::base64url_encode($encryptedData)
        );
    }

    private function _deriveSharedSecret() {
        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getNistCurves()->generator256();
        $private = $generator->createPrivateKey();
        $public = $private->getPublicKey();

        $compressingSerializer = new CompressedPointSerializer($adapter);
        $uncompressingSerializer = new UncompressedPointSerializer($adapter);
        $serialized = $compressingSerializer->serialize($public->getPoint());
        $uncompressedSerialized = $uncompressingSerializer->serialize($public->getPoint());
        $encodedUncompressed = hex2bin($this->P256ASN1Prefix . $uncompressedSerialized);

        $appPub = $compressingSerializer->unserialize(
            EccFactory::getNistCurves()->curve256(), 
            bin2hex($this->decodedAppEcdhP256Key)
        );
        $appPubUncompressed = $uncompressingSerializer->serialize($appPub);
        $appPubInstance = new PublicKey($adapter, $generator, $appPub);

        $exchange = $private->createExchange($appPubInstance);
        $shared = $exchange->calculateSharedKey();

        $kdf = function (GeneratorPoint $G, \GMP $sharedSecret, $encodedUncompressed) {
            $adapter = $G->getAdapter();
            $binary = $adapter->intToFixedSizeString(
                $sharedSecret,
                NumberSize::bnNumBytes($adapter, $G->getOrder())
            );

            $toHash = $binary . hex2bin("00000001") . $encodedUncompressed;

            $hash = hash('sha256', $toHash, true);
            return $hash;
        };

        $key = $kdf($generator, $shared, $encodedUncompressed);

        return (object) [
            'aesKey' => $key,
            'ephemeralEcdhPublicKey' => hex2bin($serialized)
        ];
    }

    private function _encryptArray($array) {
        array_walk_recursive($array, function (&$value) {
            $value = $this->_encryptString($value);
        });

        return $array;
    }

    private function _encryptString($string) {
        if (in_array($this->cipher, openssl_get_cipher_methods())) {
            $sharedSecret = $this->_deriveSharedSecret();

            $iv = $this->_generateBytes(12);
            $tag = '';
            $aad = $this->decodedAppEcdhP256Key;

            $enc = openssl_encrypt(
                $string,
                'aes-256-gcm',
                $sharedSecret->aesKey,
                OPENSSL_RAW_DATA,
                $iv,
                $tag,
                $aad,
                16
            );

            $datatype = is_numeric($string) ? 'number:' : '';
        
            return $this->_format($datatype, $sharedSecret->ephemeralEcdhPublicKey, $iv, $enc . $tag);
        } else {
            throw new EvervaultError('AES-256-GCM is not supported. Please upgrade to PHP >7.1.');
        }
    }

    public function encryptData($data) {
        if (is_array($data)) {
            return $this->_encryptArray($data);
        }

        if (is_string($data) or is_numeric($data)) {
            return $this->_encryptString($data);
        }

        throw new EvervaultError('Data is not encryptable');
    }
}