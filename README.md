## Arus // Monolog Telegram Handler for PHP 7.1+

[![Build Status](https://scrutinizer-ci.com/g/autorusltd/monolog-telegram-handler/badges/build.png?b=master)](https://scrutinizer-ci.com/g/autorusltd/monolog-telegram-handler/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/autorusltd/monolog-telegram-handler/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/autorusltd/monolog-telegram-handler/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/autorusltd/monolog-telegram-handler/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/autorusltd/monolog-telegram-handler/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/arus/monolog-telegram-handler/v/stable)](https://packagist.org/packages/arus/monolog-telegram-handler)
[![Total Downloads](https://poser.pugx.org/arus/monolog-telegram-handler/downloads)](https://packagist.org/packages/arus/monolog-telegram-handler)
[![License](https://poser.pugx.org/arus/monolog-telegram-handler/license)](https://packagist.org/packages/arus/monolog-telegram-handler)

## Installation (via composer)

```bash
composer require 'arus/monolog-telegram-handler:^2.0'
```

## How to use?

```php
use Arus\Monolog\Handler\TelegramHandler;
use Monolog\Logger;

$token = '000000000:000000000ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$recipient = '000000000';

$sender = new TelegramHandler($token, $recipient);

$logger = new Logger('app');
$logger->pushHandler($sender);

$logger->debug('Hello, world!');
```

#### Send a photo...

```php
$logger->debug('Hello, world!', [
    'photo' => 'https://example.com/photo.jpeg',
]);
```

#### Send an animation...

```php
$logger->debug('Hello, world!', [
    'animation' => 'https://example.com/photo.gif',
]);
```

#### Send a video...

```php
$logger->debug('Hello, world!', [
    'video' => 'https://example.com/photo.mp4',
]);
```

### Set custom API URL (relevant for Russia)

#### Via API

```php
$sender->setUrl('https://proxy.api.telegram.example.com');
```

#### Via environment

```php
putenv('TELEGRAM_URL=https://proxy.api.telegram.example.com');
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
