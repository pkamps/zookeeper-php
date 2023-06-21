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
    public static function fromSocket(Socket\Socket $socket, ConnectionOptions $options): self
    {
        return new self(
            $socket,
            $options->requestOptions->recvTimeout,
        );
    }

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
            ),
            $connectionOptions->requestOptions->recvTimeout,
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
    public function read(?Amp\Cancellation $cancellation = null): ?Byte\Buffer
    {
        /** @phpstan-var null|int<1, max> $len */
        $len = Byte\readFromSocket($this->socket, 4, $this->createCancellation($cancellation))?->consumeUint32();

        if (null === $len) {
            return null;
        }

        $buffer = Byte\readFromSocket($this->socket, $len, $this->createCancellation($cancellation));

        if (null === $buffer) {
            return null;
        }

        while ($len > $buffer->size()) {
            /** @phpstan-var int<1, max> $remain */
            $remain = $len - $buffer->size();

            $buffer->append(
                Byte\readFromSocket($this->socket, $remain, $this->createCancellation($cancellation)) ?: '',
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

    public function isClosed(): bool
    {
        return $this->socket->isClosed();
    }

    private function __construct(
        private readonly Socket\Socket $socket,
        private readonly float $recvTimeout,
    ) {
    }

    private function createCancellation(?Amp\Cancellation $cancellation = null): Amp\Cancellation
    {
        return $cancellation ?: new Amp\TimeoutCancellation($this->recvTimeout);
    }
}
