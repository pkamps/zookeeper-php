<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper\Protocol;

use Kafkiansky\Zookeeper\Byte;

/**
 * @internal
 * @psalm-internal Kafkiansky\Zookeeper
 *
 * @template ResponseType of ZookeeperResponse
 * @template RequestType of ZookeeperRequest<ResponseType>
 *
 * @template-implements ZookeeperRequest<Response<ResponseType>>
 */
final class Request implements ZookeeperRequest
{
    /**
     * @param positive-int $xid
     * @param RequestType  $request
     */
    public function __construct(
        private readonly int $xid,
        private readonly OpCode $opCode,
        private readonly ZookeeperRequest $request,
    ) {
    }

    public function pack(): Byte\Buffer
    {
        return (new Byte\Buffer())
            ->appendInt32($this->xid)
            ->appendOpCode($this->opCode)
            ->append($this->request->pack())
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): callable
    {
        return function (Byte\Buffer $buffer): Response {
            $response = Response::unpack($buffer);

            return ErrorCode::OK === $response->errorCode
                ? $response->withZookeeperResponse(
                    Byte\unpackResponse($this->request, $buffer),
                )
                : $response
                ;
        };
    }
}
