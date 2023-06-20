<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Auth;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperResponse;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @psalm-immutable
 */
final class AuthResponse implements ZookeeperResponse
{
    public static function unpack(Byte\Buffer $buffer): static
    {
        return new self();
    }
}
