<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper;

use Kafkiansky\Zookeeper\Network;
use Kafkiansky\Zookeeper\Protocol;

final class Node
{
    /** @var int<0, max> */
    private int $xid;

    /**
     * @param int<0, max> $xid
     */
    public function __construct(
        private readonly Network\BufferedSocket $socket,
        private readonly int $sessionId,
        private readonly string $password,
        int $xid = 0,
    ) {
        $this->xid = $xid;
    }

    /**
     * @param non-empty-string                  $path
     * @param non-empty-string                  $data
     * @param iterable<array-key, Protocol\Acl> $acls
     *
     * @throws \Amp\ByteStream\ClosedException
     * @throws \Amp\ByteStream\StreamException
     * @throws \PHPinnacle\Buffer\BufferOverflow
     */
    public function create(
        string $path,
        string $data,
        iterable $acls,
        Protocol\CreateMode $mode = Protocol\CreateMode::Persistent,
    ): string {
        $request = new Protocol\Request(
            ++$this->xid,
            Protocol\OpCode::Create,
            new Protocol\Create\CreateRequest($path, $data, [...$acls], $mode),
        );

        $this->socket->write(Byte\packRequest($request));

        /** @var Protocol\Response<Protocol\Create\CreateResponse> $response */
        $response = Byte\unpackResponse($request, $this->socket->read());

        return $response->response?->path ?: throw UnexpectedResponseReceived::fromErrorCode($response->errorCode);
    }

    /**
     * @param non-empty-string $path
     * @param non-empty-string $data
     *
     * @throws \Amp\ByteStream\ClosedException
     * @throws \Amp\ByteStream\StreamException
     * @throws \PHPinnacle\Buffer\BufferOverflow
     */
    public function set(string $path, string $data, int $version): Protocol\Stat
    {
        $request = new Protocol\Request(
            ++$this->xid,
            Protocol\OpCode::SetData,
            new Protocol\Data\SetDataRequest($path, $data, $version),
        );

        $this->socket->write(Byte\packRequest($request));

        /** @var Protocol\Response<Protocol\Data\SetDataResponse> $response */
        $response = Byte\unpackResponse($request, $this->socket->read());

        return $response->response?->stat ?: throw UnexpectedResponseReceived::fromErrorCode($response->errorCode);
    }

    /**
     * @throws \Amp\ByteStream\ClosedException
     * @throws \Amp\ByteStream\StreamException
     * @throws \PHPinnacle\Buffer\BufferOverflow
     * @throws ZookeeperException
     */
    public function addAuth(AuthScheme ...$authSchemes): void
    {
        foreach ($authSchemes as $authScheme) {
            $request = new Protocol\Request(
                ++$this->xid,
                Protocol\OpCode::Auth,
                new Protocol\Auth\AuthRequest(0, $authScheme->scheme, $authScheme->credentials),
            );

            $this->socket->write(Byte\packRequest($request));

            /** @var Protocol\Response<Protocol\Auth\AuthResponse> $response */
            $response = Byte\unpackResponse($request, $this->socket->read());

            if ($response->errorCode !== Protocol\ErrorCode::OK) {
                throw UnexpectedResponseReceived::fromErrorCode($response->errorCode);
            }
        }
    }

    public function shutdown(): void
    {
        $this->socket->shutdown();
    }
}
