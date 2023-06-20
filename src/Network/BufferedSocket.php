<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Network;

use Amp\Socket;
use Amp;
use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\ConnectionOptions;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 */
final class BufferedSocket
{
    /**
     * @throws Amp\CancelledException
     * @throws Socket\ConnectException
     */
    public static function connect(ConnectionOptions $connectionOptions): self
    {
        return new self(
            Socket\connect(
                (string) $connectionOptions,
                context: $connectionOptions->connectContext,
                cancellation: 0 === $connectionOptions->timeout
                    ? new Amp\NullCancellation()
                    : new Amp\TimeoutCancellation($connectionOptions->timeout),
            )
        );
    }

    /**
     * @throws \Amp\ByteStream\ClosedException
     * @throws \Amp\ByteStream\StreamException
     */
    public function write(Byte\Buffer $buffer): void
    {
        $this->socket->write($buffer->flush());
    }

    /**
     * @throws \PHPinnacle\Buffer\BufferOverflow
     */
    public function read(): Byte\Buffer
    {
        /** @phpstan-var int<1, max> $len */
        $len = Byte\readFromSocket($this->socket, 4)->consumeUint32();

        $buffer = Byte\readFromSocket($this->socket, $len);

        while ($len > $buffer->size()) {
            /** @phpstan-var int<1, max> $remain */
            $remain = $len - $buffer->size();

            $buffer->append(
                Byte\readFromSocket($this->socket, $remain),
            );
        }

        return $buffer;
    }

    public function shutdown(): void
    {
        if (false === $this->socket->isClosed()) {
            $this->socket->close();
        }
    }

    private function __construct(
        private readonly Socket\Socket $socket,
    ) {
    }
}
