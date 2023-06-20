<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

use Kafkiansky\Zookeeper\Byte;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template ResponseType of ZookeeperResponse
 */
interface ZookeeperRequest
{
    public function pack(): Byte\Buffer;

    /**
     * @return class-string<ResponseType>|callable(Byte\Buffer): Response<ResponseType>
     */
    public function type(): string|callable;
}
