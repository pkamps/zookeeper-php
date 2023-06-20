<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

/**
 * @psalm-immutable
 *
 * @template-implements \IteratorAggregate<Permission>
 */
final class Permission implements \IteratorAggregate
{
    public const NONE = 0b00000;
    public const READ = 0b00001;
    public const WRITE = 0b00010;
    public const CREATE = 0b00100;
    public const DELETE = 0b01000;
    public const ADMIN = 0b10000;
    public const ALL = 0b11111;

    /**
     * @phpstan-param Permission::* $flags
     */
    public static function new(int $flags): self
    {
        return new self($flags);
    }

    public function or(self $permission): self
    {
        return new self(
            $this->flags | $permission->flags,
        );
    }

    public function and(self $permission): self
    {
        return new self(
            $this->flags & $permission->flags,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        if (($this->flags & self::NONE) === self::NONE) {
            yield new self(self::NONE);
        }

        if (($this->flags & self::READ) === self::READ) {
            yield new self(self::READ);
        }

        if (($this->flags & self::WRITE) === self::WRITE) {
            yield new self(self::WRITE);
        }

        if (($this->flags & self::CREATE) === self::CREATE) {
            yield new self(self::CREATE);
        }

        if (($this->flags & self::DELETE) === self::DELETE) {
            yield new self(self::DELETE);
        }

        if (($this->flags & self::ADMIN) === self::ADMIN) {
            yield new self(self::ADMIN);
        }

        if (($this->flags & self::ALL) === self::ALL) {
            yield new self(self::ALL);
        }
    }

    private function __construct(public readonly int $flags)
    {
    }
}
