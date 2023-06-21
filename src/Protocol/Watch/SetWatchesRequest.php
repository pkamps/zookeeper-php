<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Watch;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperRequest;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template-implements ZookeeperRequest<SetWatchesResponse>
 */
final class SetWatchesRequest implements ZookeeperRequest
{
    /**
     * @param non-empty-string[] $dataWatches
     * @param non-empty-string[] $existsWatches
     * @param non-empty-string[] $childWatches
     */
    public function __construct(
        private readonly int $relativeZxid,
        private readonly array $dataWatches = [],
        private readonly array $existsWatches = [],
        private readonly array $childWatches = [],
    ) {
    }

    public function pack(): Byte\Buffer
    {
        return (new Byte\Buffer())
            ->appendInt64($this->relativeZxid)
            ->appendList($this->dataWatches, Byte\Buffer::putString(...))
            ->appendList($this->existsWatches, Byte\Buffer::putString(...))
            ->appendList($this->childWatches, Byte\Buffer::putString(...))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return SetWatchesResponse::class;
    }
}
