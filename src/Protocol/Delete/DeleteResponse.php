<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Delete;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperResponse;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 */
final class DeleteResponse implements ZookeeperResponse
{
    /**
     * {@inheritdoc}
     */
    public static function unpack(Byte\Buffer $buffer): static
    {
        return new self();
    }
}
