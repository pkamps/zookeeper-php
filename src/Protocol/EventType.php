<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

enum EventType: int
{
    case UNKNOWN               = -1000;
    case NODE_CREATED          = 1;
    case NODE_DELETED          = 2;
    case NODE_DATA_CHANGED     = 3;
    case NODE_CHILDREN_CHANGED = 4;
    case SESSION               = -1;
    case NOT_WATCHING          = -2;
}
