<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol\Auth;

use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\Protocol\ZookeeperRequest;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template-implements ZookeeperRequest<AuthResponse>
 */
final class AuthRequest implements ZookeeperRequest
{
    public function __construct(
        private readonly int $type,
        private readonly string $scheme,
        private readonly string $auth,
    ) {
    }

    public function pack(): Byte\Buffer
    {
        return (new Byte\Buffer())
            ->appendInt32($this->type)
            ->appendString($this->scheme)
            ->appendString($this->auth)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return AuthResponse::class;
    }
}
