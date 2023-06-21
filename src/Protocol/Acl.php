<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

use Kafkiansky\Zookeeper\Byte;

final class Acl
{
    public function __construct(
        public readonly Permission $perms,
        public readonly string $scheme,
        public readonly string $id,
    ) {
    }

    /**
     * @throws \PHPinnacle\Buffer\BufferOverflow
     */
    public static function fromBuffer(Byte\Buffer $buffer): self
    {
        return new self(
            Permission::new($buffer->consumeInt32()),
            $buffer->consumeString(),
            $buffer->consumeString(),
        );
    }

    public static function pack(Byte\Buffer $buffer, self $acl): void
    {
        $buffer
            ->appendInt32($acl->perms->flags)
            ->appendString($acl->scheme)
            ->appendString($acl->id)
        ;
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
