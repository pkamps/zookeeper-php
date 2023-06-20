<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper;

use Kafkiansky\Zookeeper\Protocol\ErrorCode;

final class UnexpectedResponseReceived extends \RuntimeException implements ZookeeperException
{
    public static function fromErrorCode(ErrorCode $errorCode): self
    {
        return new self(
            \sprintf('Error code "%d" (%s) received.', $errorCode->value, $errorCode->name),
        );
    }
}
