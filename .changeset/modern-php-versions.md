---
"evervault-php": major
---

Require PHP 8.4 or later, and declare the `gmp` extension the SDK has always depended on.

The `php` constraint previously claimed `^7.1|^8.0`, but the SDK has not actually run on PHP 7 since `str_ends_with()` (PHP 8.0+) was introduced in `EvervaultUtils::isDecryptionDomain()`. CI now only tests 8.4 and 8.5, so the declared floor has been raised to match what is genuinely supported and verified. `ext-gmp` was previously satisfied only as a transitive requirement of `paragonie/ecc`; it is now declared directly.

Two latent PHP 9 fatals have also been fixed: an implicitly-nullable `$previous` parameter on `EvervaultError::__construct()` (which now accepts any `\Throwable`, matching its parent), and a `null` being passed to `str_ends_with()` when `isDecryptionDomain()` was given a URL with no host.
