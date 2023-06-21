<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Byte;

use Kafkiansky\Zookeeper\Protocol\ErrorCode;
use Kafkiansky\Zookeeper\Protocol\OpCode;
use PHPinnacle\Buffer\BufferOverflow;
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
        return ErrorCode::tryFrom($this->consumeInt32()) ?: ErrorCode::UNKNOWN;
    }

    public function appendBool(bool $v): self
    {
        return $this->appendUint8((int) $v);
    }

    public static function putString(self $buffer, string $v): void
    {
        $buffer->appendString($v);
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

    /**
     * @template T
     *
     * @param callable(Buffer): T $consumer
     *
     * @throws BufferOverflow
     *
     * @return \Generator<T>
     */
    public function consumeIterator(callable $consumer): \Generator
    {
        $len = $this->consumeUint32();

        for ($i = 0; $i < $len; ++$i) {
            yield $consumer($this);
        }
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
