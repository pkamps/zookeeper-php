<?php

declare(strict_types=1);

use Kafkiansky\Zookeeper;

require_once __DIR__ . '/../vendor/autoload.php';

$zk = Zookeeper\Zookeeper::fromConnectionOptions(
    Zookeeper\ConnectionOptions::fromArray(['127.0.0.1:2181'])->withRequestOptions(
        new Zookeeper\RequestOptions(4000, new Zookeeper\AuthScheme('digest', 'user:secret')),
    ),
);

$node = $zk->node();

$node->create('/local', 'test', [Zookeeper\Protocol\Acl::openUnsafe()]);
