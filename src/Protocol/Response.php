<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

use Kafkiansky\Zookeeper\Byte;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template T of ZookeeperResponse
 */
final class Response implements ZookeeperResponse
{
    /**
     * @param ?T $response
     */
    public function __construct(
        public readonly int $xid,
        public readonly int $zxid,
        public readonly ErrorCode $errorCode,
        public readonly ?ZookeeperResponse $response = null,
    ) {
    }

    /**
     * @pure
     *
     * @template E of ZookeeperResponse
     *
     * @param E $response
     *
     * @return self<E>
     */
    public function withZookeeperResponse(ZookeeperResponse $response): self
    {
        return new self(
            $this->xid,
            $this->zxid,
            $this->errorCode,
            $response,
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function unpack(Byte\Buffer $buffer): static
    {
        /** @phpstan-ignore-next-line */
        return new self(
            $buffer->consumeInt32(),
            $buffer->consumeInt64(),
            $buffer->consumeErrorCode(),
        );
    }
}
