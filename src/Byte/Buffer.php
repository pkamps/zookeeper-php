<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Byte;

use Kafkiansky\Zookeeper\Protocol\ErrorCode;
use Kafkiansky\Zookeeper\Protocol\OpCode;
use PHPinnacle\Buffer\ByteBuffer;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 */
final class Buffer extends ByteBuffer
{
    /**
     * @throws \PHPinnacle\Buffer\BufferOverflow
     */
    public function consumeErrorCode(): ErrorCode
    {
        return ErrorCode::tryFrom($this->consumeInt32()) ?: ErrorCode::Unknown;
    }

    public function appendString(string $v): self
    {
        return $this
            ->appendUint32(\strlen($v))
            ->append($v)
            ;
    }

    public function consumeString(): string
    {
        return $this->consume($this->consumeUint32());
    }

    /**
     * @template T
     *
     * @param T[]                       $list
     * @param callable(Buffer, T): void $appender
     */
    public function appendList(array $list, callable $appender): self
    {
        $this->appendUint32(\count($list));

        foreach ($list as $item) {
            $appender($this, $item);
        }

        return $this;
    }

    public function appendOpCode(OpCode $opCode): self
    {
        return $this->appendInt32($opCode->value);
    }

    /**
     * @throws \PHPinnacle\Buffer\BufferOverflow
     */
    public function consumeOpCode(): OpCode
    {
        return OpCode::from($this->consumeInt32());
    }
}