<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Ping;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperRequest;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template-implements ZookeeperRequest<PingResponse>
 */
final class PingRequest implements ZookeeperRequest
{
    public function pack(): Byte\Buffer
    {
        return new Byte\Buffer();
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return PingResponse::class;
    }
}
