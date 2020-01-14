## Arus // Monolog Telegram Handler for PHP 7.1+ based on CLI CURL utility

[![Build Status](https://scrutinizer-ci.com/g/autorusltd/monolog-telegram-handler/badges/build.png?b=master)](https://scrutinizer-ci.com/g/autorusltd/monolog-telegram-handler/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/autorusltd/monolog-telegram-handler/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/autorusltd/monolog-telegram-handler/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/autorusltd/monolog-telegram-handler/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/autorusltd/monolog-telegram-handler/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/arus/monolog-telegram-handler/v/stable)](https://packagist.org/packages/arus/monolog-telegram-handler)
[![Total Downloads](https://poser.pugx.org/arus/monolog-telegram-handler/downloads)](https://packagist.org/packages/arus/monolog-telegram-handler)
[![License](https://poser.pugx.org/arus/monolog-telegram-handler/license)](https://packagist.org/packages/arus/monolog-telegram-handler)

## Installation (via composer)

```bash
composer require 'arus/monolog-telegram-handler:^1.1'
```

## How to use?

```php
use Arus\Monolog\Handler\TelegramHandler;
use Monolog\Logger;

$token = '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11';
$recipients = ['@channelusername'];

$sender = new TelegramHandler($token, $recipients);

$logger = new Logger('app');
$logger->pushHandler($sender);

$logger->debug('Hello, world!');
```

### Send a photo...

```php
$logger->debug('Hello, world!', [
    'photo' => 'https://...',
]);
```

### Send an animation...

```php
$logger->debug('Hello, world!', [
    'animation' => 'https://...',
]);
```

### Send a video...

```php
$logger->debug('Hello, world!', [
    'video' => 'https://...',
]);
```

### JSON encoding control

```php
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

// See: https://php.net/json_encode
// See: https://www.php.net/manual/en/json.constants.php
$sender->setJsonOptions(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

// See: https://php.net/json_encode
$sender->setJsonDepth(32);
```

### Set custom API URL

```php
$sender->setUrl('http://localhost');
```

## Test run

Create your `phpunit.xml` file:

```bash
cp phpunit.xml.dist phpunit.xml
```

Open your `phpunit.xml` file and set the following environment variables: `TELEGRAM_TOKEN` and `TELEGRAM_RECIPIENT`, then:

```bash
php vendor/bin/phpunit --colors=always --coverage-text
```

## Useful links

* https://core.telegram.org/bots/api
* https://www.php.net/manual/en/json.constants.php
