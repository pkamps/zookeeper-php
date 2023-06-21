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

var_dump($node->delete('/local', 0)->await());
var_dump($node->create('/local', 'data', Zookeeper\Protocol\Acl::openUnsafe())->await());
var_dump($node->getAcl('/local')->await());
var_dump($node->setAcl('/local', Zookeeper\Protocol\Acl::readUnsafe(), 0)->await());
var_dump($node->sync('/local')->await());

try {
    var_dump($node->delete('/local', 2)->await());
} catch (Zookeeper\UnexpectedResponseReceived $e) {
    var_dump($e->errorCode);
}

var_dump($node->delete('/local', 0)->await());

EventLoop::run();
