<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Acl;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\Acl;
use Kafkiansky\Zookeeper\Protocol\Stat;
use Kafkiansky\Zookeeper\Protocol\ZookeeperResponse;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @psalm-immutable
 */
final class GetAclResponse implements ZookeeperResponse
{
    /**
     * @param Acl[] $acls
     */
    private function __construct(
        public readonly array $acls,
        public readonly Stat $stat,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function unpack(Byte\Buffer $buffer): static
    {
        return new self(
            \iterator_to_array($buffer->consumeIterator(Acl::fromBuffer(...))),
            Stat::fromBuffer($buffer),
        );
    }
}
