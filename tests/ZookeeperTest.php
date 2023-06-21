<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Tests;

use Amp\Socket\Socket;
use Kafkiansky\Zookeeper\Byte;
use Kafkiansky\Zookeeper\ConnectionOptions;
use Kafkiansky\Zookeeper\Protocol\Connect\ConnectRequest;
use Kafkiansky\Zookeeper\Zookeeper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Zookeeper::class)]
final class ZookeeperTest extends TestCase
{
    public function testItConnected(): void
    {
        $socket = $this->createMock(Socket::class);
        $socket
            ->expects($this->once())
            ->method('write')
            ->with(
                Byte\packRequest(new ConnectRequest(1000))->flush(),
            )
        ;

        $response = (new Byte\Buffer())
            ->appendInt32(0)
            ->appendInt32(0)
            ->appendInt64(random_int(0, 10000))
            ->appendString((string) hex2bin('d6cc7beccf3e2c794dc3a274ff59d9dc'))
            ->appendBool(false)
        ;

        $socket
            ->expects($this->exactly(2))
            ->method('read')
            ->willReturnOnConsecutiveCalls(
                (new Byte\Buffer())->appendInt32($response->size())->flush(),
                $response->flush(),
            )
        ;

        $socket->expects($this->once())->method('close');

        $zk = Zookeeper::fromSocket(
            $socket,
            ConnectionOptions::fromArray(['127.0.0.1:2181']),
        );

        $node = $zk->node();
        $node->shutdown();
    }
}
