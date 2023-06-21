<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Acl;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\Stat;
use Kafkiansky\Zookeeper\Protocol\ZookeeperResponse;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @psalm-immutable
 */
final class SetAclResponse implements ZookeeperResponse
{
    private function __construct(
        public readonly Stat $stat,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function unpack(Byte\Buffer $buffer): static
    {
        return new self(
            Stat::fromBuffer($buffer),
        );
    }
}
