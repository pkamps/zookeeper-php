<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

use Kafkiansky\Zookeeper\Byte;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 */
final class Response implements ZookeeperResponse
{
    public function __construct(
        public readonly int $xid,
        public readonly int $zxid,
        public readonly ErrorCode $errorCode,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function unpack(Byte\Buffer $buffer): static
    {
        return new self(
            $buffer->consumeInt32(),
            $buffer->consumeInt64(),
            $buffer->consumeErrorCode(),
        );
    }
}
