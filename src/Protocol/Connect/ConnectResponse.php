<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Connect;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperResponse;

/**
 * @psalm-immutable
 *
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 */
final class ConnectResponse implements ZookeeperResponse
{
    private function __construct(
        public readonly int $protocolVersion,
        public readonly int $timeout,
        public readonly int $sessionId,
        public readonly string $password,
        public readonly bool $readOnly,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function unpack(Byte\Buffer $buffer): static
    {
        return new self(
            $buffer->consumeInt32(),
            $buffer->consumeInt32(),
            $buffer->consumeInt64(),
            \bin2hex($buffer->consume($buffer->consumeInt32())),
            (bool) $buffer->consumeUint8(),
        );
    }
}
