<p>
  <img src="res/logo.svg">
</p>

## Evervault PHP Bindings
### Prerequisites

To get started with the Evervault PHP SDK, you will need to have created a team on the Evervault Dashboard.

We are currently in invite-only early access. You can apply for early access [here](https://evervault.com).

### Dependencies

**The Evervault SDK requires PHP 7.1.0 or later.**

The bindings also require the following extensions:

- [`openssl`](https://www.php.net/manual/en/book.openssl.php)
- [`curl`](https://secure.php.net/manual/en/book.curl.php)
- [`json`](https://secure.php.net/manual/en/book.json.php)
- [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php)

If you install the bindings using Composer, these should automatically be installed. Otherwise, please ensure that the extensions are available on your system.

## Installation

### Composer

You can install the Evervault PHP bindings using [Composer](http://getcomposer.org/). Simply run the following command:

```sh
composer require evervault/evervault-php
```

To use the bindings, use Composer's autoload:

```php
require_once('vendor/autoload.php');
```

### Manual

If you'd prefer to not use Composer, you can download our [latest release](https://github.com/evervault/evervault-php/releases). Once downloaded, simply include the `init.php` file from the SDK's root folder.

```php
require_once('/path/to/evervault-php/init.php');
```

## Quickstart

A simple flow looks like:

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

## API Reference

### $evervault->encrypt()

`$evervault->encrypt()` lets you encrypt data for use in any of your evervault Cages. You can use it to store encrypted data to be used in a Cage at another time.

```php
$evervault->encrypt($data = array | string)
```

| Parameter | Type | Description |
| --------- | ---- | ----------- |
| `$data` | `array` or `string` | Data to be encrypted |

### $evervault->run()

`$evervault->run()` lets you invoke your Evervault Cages with a given payload.

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