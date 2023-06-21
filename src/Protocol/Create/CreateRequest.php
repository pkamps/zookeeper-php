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
        private readonly CreateMode $mode = new CreateMode(CreateMode::PERSISTENT),
        private readonly null|int $ttl = null,
    ) {
    }

    public function pack(): Byte\Buffer
    {
        $buffer = (new Byte\Buffer())
            ->appendString($this->path)
            ->appendString($this->data)
            ->appendList($this->acls, Acl::pack(...))
            ->appendInt32($this->mode->flags)
        ;

        if (null !== $this->ttl) {
            $buffer->appendInt64($this->ttl);
        }

        return $buffer;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return CreateResponse::class;
    }
}
