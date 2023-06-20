<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper;

use Amp;
use Amp\Socket;
use Kafkiansky\Zookeeper\Protocol;
use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Network;

final class Zookeeper
{
    /** @var Node[] */
    private array $nodes = [];

    public static function fromConnectionOptions(ConnectionOptions $connectionOptions): self
    {
        return new self($connectionOptions);
    }

    /**
     * @throws Amp\ByteStream\ClosedException
     * @throws Amp\ByteStream\StreamException
     * @throws Amp\CancelledException
     * @throws Socket\ConnectException
     * @throws \PHPinnacle\Buffer\BufferOverflow
     * @throws ZookeeperException
     */
    public function node(): Node
    {
        $socket = Network\BufferedSocket::connect($this->connectionOptions);

        $request = new Protocol\Connect\ConnectRequest(
            timeout: $this->connectionOptions->requestOptions->timeout,
        );

        $socket->write(
            Byte\packRequest($request),
        );

        /** @phpstan-ignore-next-line */
        $connectResponse = Byte\unpackResponse($request, $socket->read());

        if ($connectResponse->sessionId === 0) {
            throw new SessionExpiredException();
        }

        $xid = 0;

        /** @var AuthScheme $authScheme */
        foreach ($this->connectionOptions->requestOptions as $authScheme) {
            $request = new Protocol\Request(
                ++$xid,
                Protocol\OpCode::Auth,
                new Protocol\Auth\AuthRequest(0, $authScheme->scheme, $authScheme->credentials),
            );

            $socket->write(Byte\packRequest($request));

            /** @var Protocol\Response<Protocol\Auth\AuthResponse> $response */
            $response = Byte\unpackResponse($request, $socket->read());

            if ($response->errorCode !== Protocol\ErrorCode::OK) {
                throw UnexpectedResponseReceived::fromErrorCode($response->errorCode);
            }
        }

        return $this->nodes[] = new Node(
            $socket,
            $connectResponse->sessionId,
            $connectResponse->password,
            $xid,
        );
    }

    public function shutdown(): void
    {
        foreach ($this->nodes as $node) {
            $node->shutdown();
        }
    }

    private function __construct(
        private readonly ConnectionOptions $connectionOptions,
    ) {
    }
}
