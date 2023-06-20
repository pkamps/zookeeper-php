<?php

declare(strict_types=1);

use Kafkiansky\Zookeeper;
use Revolt\EventLoop;

require_once __DIR__ . '/../vendor/autoload.php';

$zk = Zookeeper\Zookeeper::fromConnectionOptions(
    Zookeeper\ConnectionOptions::fromArray(['127.0.0.1:2181']),
);

$node = $zk->node();
$node->run(sleep: 1);

Zookeeper\shutdownOnSignals($node);

dump($node->create('/local', 'data', Zookeeper\Protocol\Acl::openUnsafe())->await());

try {
    dump($node->delete('/local', 2)->await());
} catch (Zookeeper\UnexpectedResponseReceived $e) {
    dump($e->errorCode);
}

dump($node->delete('/local', 0)->await());

EventLoop::run();
