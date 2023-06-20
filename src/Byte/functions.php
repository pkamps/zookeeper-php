<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Byte;

use Amp\Cancellation;
use Amp\CancelledException;
use Amp\Socket\Socket;
use Kafkiansky\Zookeeper\Protocol;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @param int<1, max> $limit
 */
function readFromSocket(Socket $socket, int $limit, ?Cancellation $cancellation = null): ?Buffer
{
    try {
        $read = $socket->read(cancellation: $cancellation, limit: $limit);
        /** @phpstan-ignore-next-line */
    } catch (CancelledException) {
        return null;
    }

    if (null === $read) {
        return null;
    }

    return new Buffer($read);
}

/**
 * @template ResponseType of Protocol\ZookeeperResponse
 *
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @param Protocol\ZookeeperRequest<ResponseType> $request
 */
function packRequest(Protocol\ZookeeperRequest $request): Buffer
{
    $buffer = $request->pack();

    return (new Buffer())
        ->appendUint32($buffer->size())
        ->append($buffer)
    ;
}

/**
 * @template ResponseType of Protocol\ZookeeperResponse
 *
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @param Protocol\ZookeeperRequest<ResponseType> $request
 *
 * @throws \PHPinnacle\Buffer\BufferOverflow
 *
 * @return ResponseType
 */
function unpackResponse(
    Protocol\ZookeeperRequest $request,
    Buffer $buffer,
): Protocol\ZookeeperResponse {
    $type = $request->type();

    return $type::unpack($buffer);
}
