<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Delete;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperRequest;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template-implements ZookeeperRequest<DeleteResponse>
 */
final class DeleteRequest implements ZookeeperRequest
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(
        private readonly string $path,
        private readonly int $version,
    ) {
    }

    public function pack(): Byte\Buffer
    {
        return (new Byte\Buffer())
            ->appendString($this->path)
            ->appendInt32($this->version)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return DeleteResponse::class;
    }
}
