<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper;

use Kafkiansky\Zookeeper\Protocol\ErrorCode;

final class UnexpectedResponseReceived extends \RuntimeException implements ZookeeperException
{
    public readonly ErrorCode $errorCode;

    /**
     * @param non-empty-string $message
     */
    public function __construct(string $message, ErrorCode $errorCode = ErrorCode::UNKNOWN)
    {
        parent::__construct($message);
        $this->errorCode = $errorCode;
    }

    public static function fromErrorCode(ErrorCode $errorCode): self
    {
        return new self(
            \sprintf('Error code "%d" (%s) received.', $errorCode->value, $errorCode->name),
            $errorCode,
        );
    }
}
