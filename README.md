[![Evervault](https://evervault.com/evervault.svg)](https://evervault.com/)

# Evervault PHP SDK

The [Evervault](https://evervault.com) PHP SDK is a toolkit for encrypting data and using the [Evervault Encryption Platform](https://evervault.com) in PHP.

You can use our PHP SDK to:

- Encrypt data server-side
- Invoke [Functions](https://docs.evervault.com/products/function)
- Decrypt data through [Outbound Relay](https://docs.evervault.com/products/outbound-relay)

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

// Process the encrypted data in a Function
$result = $evervault->run('hello-function', $encrypted);
```

## Reference

The Evervault PHP SDK exposes two functions.

### $evervault->encrypt()

`$evervault->encrypt()` encrypts data using Evervault Encryption. Evervault Strings can be used across all of our products.

To encrypt data using the PHP SDK, simply pass a `string` or `array` into the `$evervault->encrypt()` function. 

The encrypted data can be stored in your database as normal and can be used with any of Evervault’s other services.

```php
$evervault->encrypt($data = array | string)
```

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `$data` | `array` or `string` | Data to be encrypted |

### $evervault->run()

`$evervault->run()` lets you invoke an Evervault Function with a given payload.

```php
$evervault->run($functionName = string, $data = array)
```

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `$functionName` | `string` | The name of the Function you want to run. |
| `$data` | `array` | The data you want to send to the Function. |
| `$options` | `array` | [Additional options for the Function Run.](#Function-Run-Options) |

#### Function Run Options

| Option    | Type      | Default | Description                                                                          |
| --------- | --------- | ------- | ------------------------------------------------------------------------------------ |
| `async`   | `boolean` | `false` | Run your Function in async mode. Asynchronous Function runs will be queued for processing and return a 200 OK response saying your run has been queued.          |
| `version` | `integer` | `0`  | Specify the version of your Function to run. By default, the latest version will be run. |

### $evervault->createRunToken()

`$evervault->createRunToken()` creates a single use, time bound token for invoking a Function.

```php
$evervault->createRunToken($functionName = string, $data = array or object)
```

| Parameter | Type   | Description                                          |
| --------- | ------ | ---------------------------------------------------- |
| `$functionName` | `string` | Name of the Function the Run Token should be created for |
| `$data`      | `array | object`   | Payload that the Run Token can be used with              |

### $evervault->enableOutboundRelay

`$evervault->enableOutboundRelay()` configures your application to proxy HTTPS requests using [Outbound Relay](/products/outbound-relay) for any requests sent using the cURL handler provided.

```php
$evervault->enableOutboundRelay($curlHandler = CurlHandle)
```

| Parameter | Type   | Description                                          |
| --------- | ------ | ---------------------------------------------------- |
| `$curlHandler` | `CurlHandle` | Any requests sent using the `CurlHandle` provided will be proxied through Outbound Relay. This will ignore any configuration created in the Evervault Dashboard |


## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/evervault/evervault-php.

## Feedback

Questions or feedback? [Let us know](mailto:support@evervault.com).
