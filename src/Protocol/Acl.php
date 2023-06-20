<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

final class Acl
{
    public function __construct(
        public readonly Permission $perms,
        public readonly string $scheme,
        public readonly string $id,
    ) {
    }

    public static function openUnsafe(): self
    {
        return new self(
            Permission::new(Permission::ALL),
            'world',
            'anyone',
        );
    }

    public static function readUnsafe(): self
    {
        return new self(
            Permission::new(Permission::READ),
            'world',
            'anyone',
        );
    }

    public static function creatorAll(): self
    {
        return new self(
            Permission::new(Permission::ALL),
            'auth',
            '',
        );
    }
}
