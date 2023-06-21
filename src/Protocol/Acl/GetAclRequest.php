<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Acl;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperRequest;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template-implements ZookeeperRequest<GetAclResponse>
 */
final class GetAclRequest implements ZookeeperRequest
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(
        public readonly string $path,
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
        return GetAclResponse::class;
    }
}
