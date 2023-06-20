<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Connect;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperRequest;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template-implements ZookeeperRequest<ConnectResponse>
 */
final class ConnectRequest implements ZookeeperRequest
{
    public function __construct(
        private readonly int $timeout,
        private readonly int $protocolVersion = 0,
        private readonly int $lastZxidSeen = 0,
        private readonly int $sessionId = 0,
        private readonly string $password = '',
        private readonly bool $readOnly = false,
    ) {
    }

    public function pack(): Byte\Buffer
    {
        return (new Byte\Buffer())
            ->appendInt32($this->protocolVersion)
            ->appendInt64($this->lastZxidSeen)
            ->appendInt32($this->timeout)
            ->appendInt64($this->sessionId)
            ->appendString($this->password)
            ->appendBool($this->readOnly)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return ConnectResponse::class;
    }
}
