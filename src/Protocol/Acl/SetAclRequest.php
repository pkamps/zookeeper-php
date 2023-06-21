<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Acl;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\Acl;
use Kafkiansky\Zookeeper\Protocol\ZookeeperRequest;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template-implements ZookeeperRequest<SetAclResponse>
 */
final class SetAclRequest implements ZookeeperRequest
{
    /**
     * @param non-empty-string $path
     * @param Acl[]             $acls
     */
    public function __construct(
        private readonly string $path,
        private readonly array $acls,
        private readonly int $version,
    ) {
    }

    public function pack(): Byte\Buffer
    {
        return (new Byte\Buffer())
            ->appendString($this->path)
            ->appendList($this->acls, Acl::pack(...))
            ->appendInt32($this->version)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return SetAclResponse::class;
    }
}
