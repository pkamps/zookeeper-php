<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

use Kafkiansky\Zookeeper\Byte;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 */
interface ZookeeperResponse
{
    /**
     * @throws \PHPinnacle\Buffer\BufferOverflow
     */
    public static function unpack(Byte\Buffer $buffer): static;
}
