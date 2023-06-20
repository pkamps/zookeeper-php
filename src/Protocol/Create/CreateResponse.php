<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Create;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperResponse;

/**
 * @psalm-immutable
 *
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 */
final class CreateResponse implements ZookeeperResponse
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
        return new self(
            $buffer->consumeString(),
        );
    }
}
