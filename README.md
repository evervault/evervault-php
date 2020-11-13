<p>
  <a href="https://welcome.evervault.com/"><img src="res/logo.svg"></a>
</p>

# Evervault PHP SDK

The [Evervault](https://evervault.com) PHP SDK is a toolkit for encrypting data as it enters your server, and working with Cages.

## Getting Started

Before starting with the Evervault PHP SDK, you will need to [create an account](https://app.evervault.com/register) and a team.

For full installation support, [book time here](https://calendly.com/evervault/cages-onboarding).

## Documentation

See the Evervault [PHP SDK documentation](https://docs.evervault.com/php).

## Dependencies

**The Evervault SDK requires PHP 7.1.0 or later.**

The bindings also require the following extensions:

- [`openssl`](https://www.php.net/manual/en/book.openssl.php)
- [`curl`](https://secure.php.net/manual/en/book.curl.php)
- [`json`](https://secure.php.net/manual/en/book.json.php)
- [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php)

If you install the bindings using [Composer](http://getcomposer.org/), these should automatically be installed. Otherwise, ensure that the extensions are available on your system.

## Installation

There are two ways to install the PHP SDK.

### 1. With Composer

You can install the Evervault PHP bindings using [Composer](http://getcomposer.org/). Simply run the following command:

```sh
composer require evervault/evervault-php
```

To use the bindings, use Composer's autoload:

```php
require_once('vendor/autoload.php');
```

### 2. By yourself

If you'd prefer to not use Composer, you can download our [latest release](https://github.com/evervault/evervault-php/releases). Once downloaded, simply include the `init.php` file from the SDK's root folder.

```php
require_once('/path/to/evervault-php/init.php');
```

## Setup

To make Evervault available for use in your app:

```php
use \Evervault\Evervault;

// Insert your API key here
$evervault = new Evervault('MTcy:B1s8/8LRiKG/ARvonWEWLgTQJRoqpVPzZQ47KB8gKlo=');

// Encrypt your sensitive data
$encrypted = $evervault->encrypt([
    'name' => 'Alice'
]);

// Process the encrypted data in a Cage
$result = $evervault->run('hello-cage', $encrypted);
```

## Reference

At present, there are two functions available in the PHP SDK: `$evervault->encrypt()` and `$evervault->run()`.

### $evervault->encrypt()

`$evervault->encrypt()` encrypts data for use in your [Cages](https://docs.evervault.com/tutorial). To encrypt data at the server, simply pass in an `array` or a `string` into the `$evervault->encrypt()` function. Store the encrypted data in your database as normal.

```php
$evervault->encrypt($data = array | string)
```

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `$data` | `array` or `string` | Data to be encrypted |

### $evervault->run()

`$evervault->run()` invokes a Cage with a given payload.

```php
$evervault->run($cageName = string, $data = array)
```

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `$cageName` | `string` | Name of the Cage to run. |
| `$data` | `array` | Payload for the Cage. |
| `$options` | `array` | [Options for the Cage run.](#Cage-Run-Options) |

#### Cage Run Options

| Option    | Type      | Default | Description                                                                          |
| --------- | --------- | ------- | ------------------------------------------------------------------------------------ |
| `async`   | `Boolean` | `False` | Run your Cage in async mode. Async Cage runs will be queued for processing.          |
| `version` | `Integer` | `Null`  | Specify the version of your Cage to run. By default, the latest version will be run. |

## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/evervault/evervault-php.

## Feedback

Questions or feedback? [Let us know](mailto:support@evervault.com).
