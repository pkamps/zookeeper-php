<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Create;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\Acl;
use Kafkiansky\Zookeeper\Protocol\CreateMode;
use Kafkiansky\Zookeeper\Protocol\ZookeeperRequest;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template-implements ZookeeperRequest<CreateResponse>
 */
final class CreateRequest implements ZookeeperRequest
{
    /**
     * @param non-empty-string $path
     * @param non-empty-string $data
     * @param Acl[]            $acls
     */
    public function __construct(
        private readonly string $path,
        private readonly string $data,
        private readonly array $acls,
        private readonly CreateMode $mode = CreateMode::Persistent,
    ) {
    }

    public function pack(): Byte\Buffer
    {
        return (new Byte\Buffer())
            ->appendString($this->path)
            ->appendString($this->data)
            ->appendList($this->acls, function (Byte\Buffer $buffer, Acl $acl): void {
                $buffer
                    ->appendInt32($acl->perms->flags)
                    ->appendString($acl->scheme)
                    ->appendString($acl->id)
                ;
            })
            ->appendInt32($this->mode->value)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return CreateResponse::class;
    }
}
