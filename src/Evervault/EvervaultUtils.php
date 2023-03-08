<?php

namespace Evervault;

class EvervaultUtils {
    public static function uuidv4() {
        $data = openssl_random_pseudo_bytes(16);  
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function base64url_encode($data) {
        $base64 = base64_encode($data);

        return preg_replace('/={1,2}$/', '', $base64);
    }

    public static function isDecryptionDomain($domain, $decryptionDomains) {
        $domain = parse_url($domain)['host'];

        foreach ($decryptionDomains as $decryptionDomain) {
            if ($decryptionDomain === $domain) {
                return true;
            } else if (substr($decryptionDomain, 0, 1) === '*' && str_ends_with($domain, substr($decryptionDomain, 1))) {
                return true;
            }
        }

        return false;
    }
}