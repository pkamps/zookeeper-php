<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

final class CreateMode
{
    /** @var int The znode will not be automatically deleted upon client's disconnect. */
    public const PERSISTENT = 0;

    /** @var int The znode will be deleted upon the client's disconnect. */
    public const EPHEMERAL = 1;

    /**
     * @var int The name of the znode will be appended with a monotonically increasing number. The actual
     * path name of a sequential node will be the given path plus a suffix `"i"` where *i* is the
     * current sequential number of the node. The sequence number is always fixed length of 10
     * digits, 0 padded. Once such a node is created, the sequential number will be incremented by
     * one.
     */
    public const SEQUENCE = 2;

    /**
     * @var int Container nodes are special purpose nodes useful for recipes such as leader, lock, etc. When
     * the last child of a container is deleted, the container becomes a candidate to be deleted by
     * the server at some point in the future. Given this property, you should be prepared to get
     * `ZkError::NoNode` when creating children inside of this container node.
     */
    public const CONTAINER = 4;

    public function __construct(
        public readonly int $flags,
    ) {
    }

    public static function new(
        int $flags,
    ): self {
        return new self($flags);
    }

    public function and(self $mode): self
    {
        return new self(
            $this->flags & $mode->flags,
        );
    }

    public function or(self $mode): self
    {
        return new self(
            $this->flags | $mode->flags,
        );
    }
}
