<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Watch;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperResponse;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @psalm-immutable
 */
final class SetWatchesResponse implements ZookeeperResponse
{
    /**
     * {@inheritdoc}
     */
    public static function unpack(Byte\Buffer $buffer): static
    {
        return new self();
    }
}
