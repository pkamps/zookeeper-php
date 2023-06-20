<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Byte;

use Amp\Socket\Socket;
use Kafkiansky\Zookeeper\Network\ByteReadException;
use Kafkiansky\Zookeeper\Protocol;

/**
 * @throws ByteReadException
 *
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @param int<1, max> $limit
 */
function readFromSocket(Socket $socket, int $limit): Buffer
{
    $read = $socket->read(limit: $limit);

    if (null === $read) {
        throw new ByteReadException('The read bytes is null.');
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
 * @return ResponseType|Protocol\Response<ResponseType>
 */
function unpackResponse(
    Protocol\ZookeeperRequest $request,
    Buffer $buffer,
): Protocol\ZookeeperResponse|Protocol\Response {
    $type = $request->type();

    return match (true) {
        \is_callable($type) => $type($buffer),
        default => $type::unpack($buffer),
    };
}
