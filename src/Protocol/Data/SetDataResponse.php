<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Data;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\Stat;
use Kafkiansky\Zookeeper\Protocol\ZookeeperResponse;

/**
 * @psalm-immutable
 *
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 */
final class SetDataResponse implements ZookeeperResponse
{
    public function __construct(
        public readonly Stat $stat,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function unpack(Byte\Buffer $buffer): static
    {
        return new self(Stat::fromBuffer($buffer));
    }
}
