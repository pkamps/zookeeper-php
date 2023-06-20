<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 */
enum OpCode: int
{
    case Notification = 0;
    case Create = 1;
    case Delete = 2;
    case Exists = 3;
    case GetData = 4;
    case SetData = 5;
    case GetACL = 6;
    case SetACL = 7;
    case GetChildren = 8;
    case Sync = 9;
    case Ping = 11;
    case GetChildren2 = 12;
    case Check = 13;
    case Multi = 14;
    case Auth = 100;
    case SetWatches = 101;
    case Sasl = 102;
    case CreateSession = -10;
    case CloseSession = -11;
    case Error = -1;
}
