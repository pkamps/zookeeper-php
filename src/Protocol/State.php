<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

enum State: int
{
    case UNKNOWN             = -1;
    case DISCONNECTED        = 0;
    case CONNECTING          = 1;
    case AUTH_FAILED         = 4;
    case CONNECTED_READ_ONLY = 5;
    case SASL_AUTHENTICATED  = 6;
    case EXPIRES             = -112;
    case CONNECTED           = 100;
    case HAS_SESSION         = 101;
}
