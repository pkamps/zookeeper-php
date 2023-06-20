<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 */
enum OpCode: int
{
    case NOTIFICATION     = 0;
    case CREATE           = 1;
    case DELETE           = 2;
    case EXISTS           = 3;
    case GET_DATA         = 4;
    case SET_DATA         = 5;
    case GET_ACL          = 6;
    case SET_ACL          = 7;
    case GET_CHILDREN     = 8;
    case SYNC             = 9;
    case PING             = 11;
    case GET_CHILDREN_2   = 12;
    case CHECK            = 13;
    case MULTI            = 14;
    case RECONFIG         = 16;
    case CREATE_CONTAINER = 19;
    case CREATE_TTL       = 21;
    case AUTH             = 100;
    case SET_WATCHES      = 101;
    case SASL             = 102;
    case CREATE_SESSION   = -10;
    case CLOSE_SESSION    = -11;
    case ERROR            = -1;
}
