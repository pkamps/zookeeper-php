<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

enum ErrorCode: int
{
    case Unknown                 = -1000;
    case OK                      = 0;
    case SystemError             = -1;
    case RuntimeInconsistency    = -2;
    case DataInconsistency       = -3;
    case ConnectionLoss          = -4;
    case MarshallingError        = -5;
    case Unimplemented           = -6;
    case OperationTimeout        = -7;
    case BadArguments            = -8;
    case InvalidState            = -9;
    case APIError                = -100;
    case NoNode                  = -101;
    case NoAuth                  = -102;
    case BadVersion              = -103;
    case NoChildrenForEphemerals = -108;
    case NodeExists              = -110;
    case NotEmpty                = -111;
    case SessionExpired          = -112;
    case InvalidCallback         = -113;
    case InvalidAcl              = -114;
    case AuthFailed              = -115;
    case Closing                 = -116;
    case Nothing                 = -117;
    case SessionMoved            = -118;
    case ZReconfigDisabled       = -123;
}
