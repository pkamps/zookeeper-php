<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper;

use Amp\NullCancellation;
use Kafkiansky\Zookeeper\Network\BufferedSocket;
use Kafkiansky\Zookeeper\Protocol\Connect\ConnectRequest;
use Kafkiansky\Zookeeper\Protocol\Connect\ConnectResponse;
use Revolt\EventLoop;
use Revolt\EventLoop\UnsupportedFeatureException;

/**
 * @param int[] $signals
 *
 * @throws UnsupportedFeatureException
 */
function shutdownOnSignals(Node $node, array $signals = [\SIGINT, \SIGTERM]): Node
{
    foreach ($signals as $signal) {
        EventLoop::unreference(
            EventLoop::onSignal($signal, $node->shutdown(...)),
        );
    }

    return $node;
}

/**
 * @throws \Amp\ByteStream\ClosedException
 * @throws \Amp\ByteStream\StreamException
 * @throws \PHPinnacle\Buffer\BufferOverflow
 */
function connect(ConnectRequest $request, BufferedSocket $socket): ConnectResponse
{
    $socket->write(Byte\packRequest($request));

    $connectBuffer = $socket->read(new NullCancellation());

    if (null === $connectBuffer) {
        throw new UnexpectedResponseReceived('Response is null.');
    }

    $connectResponse = Byte\unpackResponse($request, $connectBuffer);

    if ($connectResponse->sessionId === 0) {
        throw new SessionExpiredException();
    }

    return $connectResponse;
}
