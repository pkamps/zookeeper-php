<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

use Kafkiansky\Zookeeper\Byte;

final class WatcherEvent
{
    private function __construct(
        public readonly EventType $type,
        public readonly State $state,
        public readonly string $path,
    ) {
    }

    /**
     * @throws \PHPinnacle\Buffer\BufferOverflow
     */
    public static function fromBuffer(Byte\Buffer $buffer): self
    {
        return new self(
            EventType::tryFrom($buffer->consumeInt32()) ?: EventType::UNKNOWN,
            State::tryFrom($buffer->consumeInt32()) ?: State::UNKNOWN,
            $buffer->consumeString(),
        );
    }
}
