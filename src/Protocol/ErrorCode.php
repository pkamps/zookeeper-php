<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

enum ErrorCode: int
{
    case UNKNOWN                    = -1000;
    case OK                         = 0;
    case SYSTEM_ERROR               = -1;
    case RUNTIME_INCONSISTENCY      = -2;
    case DATA_INCONSISTENCY         = -3;
    case CONNECTION_LOSS            = -4;
    case MARSHALLING_ERROR          = -5;
    case UNIMPLEMENTED              = -6;
    case OPERATION_TIMEOUT          = -7;
    case BAD_ARGUMENTS              = -8;
    case INVALID_STATE              = -9;
    case API_ERROR                  = -100;
    case NO_NODE                    = -101;
    case NO_AUTH                    = -102;
    case BAD_VERSION                = -103;
    case NO_CHILDREN_FOR_EPHEMERALS = -108;
    case NODE_EXISTS                = -110;
    case NOT_EMPTY                  = -111;
    case SESSION_EXPIRED            = -112;
    case INVALID_CALLBACK           = -113;
    case INVALID_ACL                = -114;
    case AUTH_FAILED                = -115;
    case CLOSING                    = -116;
    case NOTHING                    = -117;
    case SESSION_MOVED              = -118;
    case Z_RECONFIG_DISABLED        = -123;
}
