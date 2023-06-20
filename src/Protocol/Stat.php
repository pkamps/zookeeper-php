<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

use Kafkiansky\Zookeeper\Byte\Buffer;

final class Stat
{
    /**
     * @param int $czxid The zxid of the change that caused this znode to be created.
     * @param int $mzxid The zxid of the change that last modified this znode.
     * @param int $ctime The time in milliseconds from epoch when this znode was created.
     * @param int $mtime The time in milliseconds from epoch when this znode was last modified.
     * @param int $version The number of changes to the data of this znode.
     * @param int $cversion The number of changes to the children of this znode.
     * @param int $aversion The number of changes to the ACL of this znode.
     * @param int $ephemeralOwner The session id of the owner of this znode if the znode is an ephemeral node. If it is not an ephemeral node, it will be zero.
     * @param int $dataLength The length of the data field of this znode.
     * @param int $numChildren The number of children of this znode.
     * @param int $pzxid
     */
    private function __construct(
        public readonly int $czxid,
        public readonly int $mzxid,
        public readonly int $ctime,
        public readonly int $mtime,
        public readonly int $version,
        public readonly int $cversion,
        public readonly int $aversion,
        public readonly int $ephemeralOwner,
        public readonly int $dataLength,
        public readonly int $numChildren,
        public readonly int $pzxid,
    ) {
    }

    /**
     * @throws \PHPinnacle\Buffer\BufferOverflow
     */
    public static function fromBuffer(Buffer $buffer): self
    {
        return new self(
            $buffer->consumeInt64(),
            $buffer->consumeInt64(),
            $buffer->consumeInt64(),
            $buffer->consumeInt64(),
            $buffer->consumeInt32(),
            $buffer->consumeInt32(),
            $buffer->consumeInt32(),
            $buffer->consumeInt64(),
            $buffer->consumeInt32(),
            $buffer->consumeInt32(),
            $buffer->consumeInt64(),
        );
    }
}
