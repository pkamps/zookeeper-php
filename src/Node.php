<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper;

use Amp\ByteStream\ClosedException;
use Amp\ByteStream\StreamException;
use Amp\Cancellation;
use Amp\DeferredFuture;
use Amp\Future;
use Amp\SignalCancellation;
use Kafkiansky\Zookeeper\Byte\Buffer;
use Kafkiansky\Zookeeper\Network;
use Kafkiansky\Zookeeper\Protocol;
use function Amp\async;
use function Amp\delay;

/**
 * @api
 */
final class Node
{
    /** @var int<0, max> */
    private int $xid;

    /** @phpstan-ignore-next-line */
    private int $zxid = 0;

    /** @var array<int, callable(Buffer, ?Protocol\ErrorCode): void> */
    private array $futures = [];

    /**
     * @param positive-int $timeout
     * @param int<0, max>  $xid
     */
    public function __construct(
        private readonly Network\BufferedSocket $socket,
        private readonly int $timeout,
        private int $sessionId,
        private string $password,
        int $xid = 0,
    ) {
        $this->xid = $xid;
    }

    /**
     * @param non-empty-string                                       $path
     * @param non-empty-string                                       $data
     * @phpstan-param iterable<array-key, Protocol\Acl>|Protocol\Acl $acls
     *
     * @throws StreamException
     * @throws ClosedException
     *
     * @return Future<string>
     */
    public function create(
        string $path,
        string $data,
        iterable|Protocol\Acl $acls,
        Protocol\CreateMode $mode = new Protocol\CreateMode(Protocol\CreateMode::PERSISTENT),
        ?int $ttl = null,
    ): Future {
        /** @var Future<string> */
        return $this->await(
            null === $ttl ? Protocol\OpCode::CREATE : Protocol\OpCode::CREATE_TTL,
            new Protocol\Create\CreateRequest($path, $data, \is_iterable($acls) ? [...$acls] : [$acls], $mode, $ttl),
            static function (DeferredFuture $future, Protocol\Create\CreateResponse $response): void {
                $future->complete($response->path);
            },
        );
    }

    /**
     * @param non-empty-string $path
     * @param non-empty-string $data
     *
     * @throws ClosedException
     * @throws StreamException
     *
     * @return Future<Protocol\Stat>
     */
    public function set(string $path, string $data, int $version): Future
    {
        /** @var Future<Protocol\Stat> */
        return $this->await(
            Protocol\OpCode::SET_DATA,
            new Protocol\Data\SetDataRequest($path, $data, $version),
            function (DeferredFuture $future, Protocol\Data\SetDataResponse $response): void {
                $future->complete($response->stat);
            },
        );
    }


    /**
     * @throws ClosedException
     * @throws StreamException
     *
     * @return \Generator<Future<void>>
     */
    public function addAuth(AuthScheme ...$authSchemes): \Generator
    {
        foreach ($authSchemes as $authScheme) {
            yield $this->await(
                Protocol\OpCode::AUTH,
                new Protocol\Auth\AuthRequest(0, $authScheme->scheme, $authScheme->credentials),
                self::noop(...),
            );
        }
    }

    /**
     * @param non-empty-string $path
     *
     * @throws ClosedException
     * @throws StreamException
     *
     * @return Future<array{string, Protocol\Stat}>
     */
    public function get(string $path, bool $watch = false): Future
    {
        /** @var Future<array{string, Protocol\Stat}> */
        return $this->await(
            Protocol\OpCode::GET_DATA,
            new Protocol\Data\GetDataRequest($path, $watch),
            static function (DeferredFuture $future, Protocol\Data\GetDataResponse $response): void {
                $future->complete([
                    $response->data,
                    $response->stat,
                ]);
            },
        );
    }

    /**
     * @param non-empty-string $path
     *
     * @throws StreamException
     * @throws ClosedException
     *
     * @return Future<bool>
     */
    public function delete(string $path, int $version): Future
    {
        /** @var Future<bool> */
        return $this->await(
            Protocol\OpCode::DELETE,
            new Protocol\Delete\DeleteRequest($path, $version),
            static function (DeferredFuture $future): void {
                $future->complete(true);
            },
            static function (DeferredFuture $future, Protocol\ErrorCode $errorCode): void {
                if (Protocol\ErrorCode::NO_NODE === $errorCode) {
                    $future->complete(false);
                    return;
                }

                self::throwError($future, $errorCode);
            },
        );
    }

    /**
     * @param non-empty-string $path
     *
     * @throws StreamException
     * @throws ClosedException
     *
     * @return Future<?Protocol\Stat>
     */
    public function exists(string $path, bool $watch = false): Future
    {
        /** @var Future<?Protocol\Stat> */
        return $this->await(
            Protocol\OpCode::EXISTS,
            new Protocol\Exists\ExistsRequest($path, $watch),
            static function (DeferredFuture $future, Protocol\Exists\ExistsResponse $response): void {
                $future->complete($response->stat);
            },
            static function (DeferredFuture $future, Protocol\ErrorCode $errorCode): void {
                if (Protocol\ErrorCode::NO_NODE === $errorCode) {
                    $future->complete();
                    return;
                }

                self::throwError($future, $errorCode);
            },
        );
    }

    /**
     * @param non-empty-string $path
     *
     * @throws StreamException
     * @throws ClosedException
     *
     * @return Future<array{Protocol\Acl[], Protocol\Stat}>
     */
    public function getAcl(string $path): Future
    {
        /** @var Future<array{Protocol\Acl[], Protocol\Stat}> */
        return $this->await(
            Protocol\OpCode::GET_ACL,
            new Protocol\Acl\GetAclRequest($path),
            static function (DeferredFuture $future, Protocol\Acl\GetAclResponse $response): void {
                $future->complete([
                    $response->acls,
                    $response->stat,
                ]);
            },
        );
    }

    /**
     * @param non-empty-string                                       $path
     * @phpstan-param iterable<array-key, Protocol\Acl>|Protocol\Acl $acls
     *
     * @throws StreamException
     * @throws ClosedException
     *
     * @return Future<Protocol\Stat>
     */
    public function setAcl(
        string $path,
        iterable|Protocol\Acl $acls,
        int $version,
    ): Future {
        /** @var Future<Protocol\Stat> */
        return $this->await(
            Protocol\OpCode::SET_ACL,
            new Protocol\Acl\SetAclRequest($path, \is_iterable($acls) ? [...$acls] : [$acls], $version),
            static function (DeferredFuture $future, Protocol\Acl\SetAclResponse $response): void {
                $future->complete($response->stat);
            },
        );
    }

    /**
     * @param non-empty-string $path
     *
     * @throws StreamException
     * @throws ClosedException
     *
     * @return Future<string>
     */
    public function sync(string $path): Future
    {
        /** @var Future<string> */
        return $this->await(
            Protocol\OpCode::SYNC,
            new Protocol\Sync\SyncRequest($path),
            static function (DeferredFuture $future, Protocol\Sync\SyncResponse $response): void {
                $future->complete($response->path);
            },
        );
    }

    /**
     * @throws ClosedException
     * @throws StreamException
     */
    public function ping(): void
    {
        $this->socket->write(
            Byte\packRequest(new Protocol\Request(++$this->xid, Protocol\OpCode::PING, new Protocol\Ping\PingRequest())),
        );
    }

    /**
     * @param null|callable(Protocol\WatcherEvent): void $eventListener
     * @param null|float                                 $sleep Sleep between waiting frames from socket. In seconds.
     */
    public function run(
        Cancellation $cancellation = new SignalCancellation([\SIGINT, \SIGTERM]),
        ?callable $eventListener = null,
        ?float $sleep = null,
    ): void {
        async(function () use ($cancellation, $eventListener, $sleep): void {
            while (false === $cancellation->isRequested() || false === $this->socket->isClosed()) {
                if (null !== ($buffer = $this->socket->read())) {
                    $response = Protocol\Response::unpack($buffer);

                    if ($response->xid === -1 && null !== $eventListener) {
                        $eventListener(Protocol\WatcherEvent::fromBuffer($buffer));
                    } elseif ($response->xid > 0) {
                        if (isset($this->futures[$response->xid])) {
                            $completion = $this->futures[$response->xid];
                            unset($this->futures[$response->xid]);

                            $completion($buffer, $response->errorCode);
                        }

                        $this->zxid = $response->zxid;
                    }

                    /** If there is data on the socket, we should not call delay. Instead, we should read the data from the socket as fast as we can. */
                    continue;
                }

                if (null !== $sleep) {
                    delay($sleep);
                }
            }
        });
    }

    public function shutdown(): void
    {
        $this->socket->shutdown();
    }

    /**
     * @template TReturn
     * @template ResponseType of Protocol\ZookeeperResponse
     *
     * @param Protocol\ZookeeperRequest<ResponseType>                          $zookeeperRequest
     * @param callable(DeferredFuture<TReturn>, ResponseType): void            $completion
     * @param null|callable(DeferredFuture<TReturn>, Protocol\ErrorCode): void $exceptional
     *
     * @throws ClosedException
     * @throws StreamException
     *
     * @return Future<TReturn>
     */
    private function await(
        Protocol\OpCode $opCode,
        Protocol\ZookeeperRequest $zookeeperRequest,
        callable $completion,
        ?callable $exceptional = null,
    ): Future {
        /** @phpstan-var DeferredFuture<TReturn> $deferred */
        $deferred = new DeferredFuture();

        $xid = ++$this->xid;

        $request = new Protocol\Request(
            $xid,
            $opCode,
            $zookeeperRequest,
        );

        $this->socket->write(
            Byte\packRequest($request),
        );

        $this->futures[$xid] = static function (
            Buffer $buffer,
            ?Protocol\ErrorCode $errorCode = null
        ) use (
            &$deferred,
            $completion,
            $exceptional,
            $zookeeperRequest,
        ): void {
            if (null !== $errorCode && Protocol\ErrorCode::OK !== $errorCode) {
                if (null === $exceptional) {
                    $exceptional = self::throwError(...);
                }

                $exceptional($deferred, $errorCode);

                return;
            }

            $completion(
                $deferred,
                Byte\unpackResponse($zookeeperRequest, $buffer),
            );
        };

        return $deferred->getFuture();
    }

    /**
     * @param DeferredFuture<void> $future
     */
    private static function noop(DeferredFuture $future): void
    {
        $future->complete();
    }

    /**
     * @param DeferredFuture<mixed> $future
     */
    private static function throwError(DeferredFuture $future, Protocol\ErrorCode $errorCode): void
    {
        $future->error(
            UnexpectedResponseReceived::fromErrorCode($errorCode),
        );
    }
}
