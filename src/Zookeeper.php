<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper;

use Amp;
use Amp\Socket;
use Kafkiansky\Zookeeper\Protocol;
use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Network;

/**
 * @api
 */
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

        $connectResponse = namespace\connect(
            new Protocol\Connect\ConnectRequest(
                timeout: $this->connectionOptions->requestOptions->timeout,
            ),
            $socket,
        );

        $xid = 0;

        /** @var AuthScheme $authScheme */
        foreach ($this->connectionOptions->requestOptions as $authScheme) {
            $request = new Protocol\Request(
                ++$xid,
                Protocol\OpCode::AUTH,
                new Protocol\Auth\AuthRequest(0, $authScheme->scheme, $authScheme->credentials),
            );

            $socket->write(Byte\packRequest($request));

            $authBuffer = $socket->read();
            \assert(null !== $authBuffer, 'Auth response must not be empty.');

            /** @var Protocol\Response $response */
            $response = Byte\unpackResponse($request, $authBuffer);

            if ($response->errorCode !== Protocol\ErrorCode::OK) {
                throw UnexpectedResponseReceived::fromErrorCode($response->errorCode);
            }
        }

        return $this->nodes[] = new Node(
            $socket,
            $this->connectionOptions->requestOptions->timeout,
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
