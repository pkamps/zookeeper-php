<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Data;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperRequest;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template-implements ZookeeperRequest<SetDataResponse>
 */
final class SetDataRequest implements ZookeeperRequest
{
    /**
     * @param non-empty-string $path
     * @param non-empty-string $data
     */
    public function __construct(
        private readonly string $path,
        private readonly string $data,
        private readonly int $version,
    ) {
    }

    public function pack(): Byte\Buffer
    {
        return (new Byte\Buffer())
            ->appendString($this->path)
            ->appendString($this->data)
            ->appendInt32($this->version)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return SetDataResponse::class;
    }
}
