<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Sync;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperRequest;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template-implements ZookeeperRequest<SyncResponse>
 */
final class SyncRequest implements ZookeeperRequest
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(
        private readonly string $path,
    ) {
    }

    public function pack(): Byte\Buffer
    {
        return (new Byte\Buffer())
            ->appendString($this->path)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return SyncResponse::class;
    }
}
