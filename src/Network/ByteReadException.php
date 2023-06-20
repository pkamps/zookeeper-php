<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Network;

use Kafkiansky\Zookeeper\ZookeeperException;

final class ByteReadException extends \RuntimeException implements ZookeeperException
{
}
