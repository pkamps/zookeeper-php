## Async Zookeeper PHP client.

## Requirements

- PHP 8.1 or higher.

## Installation

The package could be installed with composer:

```shell
composer require kafkiansky/zookeeper-php
```

## Getting started

```php
<?php

declare(strict_types=1);

use Kafkiansky\Zookeeper;

require_once __DIR__ . '/../vendor/autoload.php';

$zk = Zookeeper\Zookeeper::fromConnectionOptions(
    Zookeeper\ConnectionOptions::fromArray(['127.0.0.1:2181']),
);

$node = $zk->node();
$node->run();

var_dump($node->create('/local', 'data', Zookeeper\Protocol\Acl::openUnsafe())->await());
var_dump($node->exists('/local')->await());

$node->shutdown();
```
### Listening watch events

```php
<?php

declare(strict_types=1);

use Kafkiansky\Zookeeper;
use Revolt\EventLoop;

require_once __DIR__ . '/../vendor/autoload.php';

$zk = Zookeeper\Zookeeper::fromConnectionOptions(
    Zookeeper\ConnectionOptions::fromArray(['127.0.0.1:2181']),
);

$node = $zk->node();
$node->run(eventListener: function (Zookeeper\Protocol\WatcherEvent $event): void {
    //
});

Zookeeper\shutdownOnSignals($node);

var_dump($node->create('/local', 'data', Zookeeper\Protocol\Acl::openUnsafe())->await());
var_dump($node->exists('/local', true)->await());

EventLoop::run();
```

## Testing

``` bash
$ composer phpunit
```  

## License

The MIT License (MIT). See [License File](LICENSE.md) for more information.