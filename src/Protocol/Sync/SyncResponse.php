<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Sync;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperResponse;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @psalm-immutable
 */
final class SyncResponse implements ZookeeperResponse
{
    private function __construct(
        public readonly string $path,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function unpack(Byte\Buffer $buffer): static
    {
        return new self($buffer->consumeString());
    }
}
