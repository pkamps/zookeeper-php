<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

enum CreateMode: int
{
    // The znode will not be automatically deleted upon client's disconnect.
    case Persistent = 0;

    // The znode will be deleted upon the client's disconnect.
    case Ephemeral = 1;

    // The name of the znode will be appended with a monotonically increasing number. The actual
    // path name of a sequential node will be the given path plus a suffix `"i"` where *i* is the
    // current sequential number of the node. The sequence number is always fixed length of 10
    // digits, 0 padded. Once such a node is created, the sequential number will be incremented by
    // one.
    case PersistentSequential = 2;

    // The znode will be deleted upon the client's disconnect, and its name will be appended with a
    // monotonically increasing number.
    case EphemeralSequential = 3;

    // Container nodes are special purpose nodes useful for recipes such as leader, lock, etc. When
    // the last child of a container is deleted, the container becomes a candidate to be deleted by
    // the server at some point in the future. Given this property, you should be prepared to get
    // `ZkError::NoNode` when creating children inside of this container node.
    case Container = 4;
}
