<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Data;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperRequest;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template-implements ZookeeperRequest<GetDataResponse>
 */
final class GetDataRequest implements ZookeeperRequest
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(
        private readonly string $path,
        private readonly bool $watch,
    ) {
    }

    public function pack(): Byte\Buffer
    {
        return (new Byte\Buffer())
            ->appendString($this->path)
            ->appendBool($this->watch)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return GetDataResponse::class;
    }
}
