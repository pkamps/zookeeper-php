<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper;

use Amp\Socket\ConnectContext;

/**
 * @template-implements \IteratorAggregate<non-empty-string>
 */
final class ConnectionOptions implements
    \IteratorAggregate,
    \Stringable
{
    /**
     * @param non-empty-list<non-empty-string> $hosts
     */
    public static function fromArray(array $hosts): self
    {
        return new self($hosts);
    }

    /**
     * @param non-empty-string $host
     */
    public static function fromString(string $host): self
    {
        /** @var non-empty-list<non-empty-string> $hosts */
        $hosts = \array_map('trim', \explode(',', $host));

        return self::fromArray($hosts);
    }

    /**
     * @param int<0, max> $timeout
     */
    public function withTimeout(int $timeout): self
    {
        return new self(
            $this->hosts,
            $timeout,
            $this->connectContext,
            $this->requestOptions,
        );
    }

    public function withConnectContext(ConnectContext $context): self
    {
        return new self(
            $this->hosts,
            $this->timeout,
            $context,
            $this->requestOptions,
        );
    }

    public function withRequestOptions(RequestOptions $options): self
    {
        return new self(
            $this->hosts,
            $this->timeout,
            $this->connectContext,
            $options,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        yield from $this->hosts;
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->hosts[\mt_rand(0, \count($this->hosts) - 1)];
    }

    /**
     * @param non-empty-list<non-empty-string> $hosts
     * @param int<0, max>                      $timeout in seconds. Zero means no timeout.
     */
    private function __construct(
        public readonly array $hosts,
        public readonly int $timeout = 10,
        public readonly ?ConnectContext $connectContext = null,
        public readonly RequestOptions $requestOptions = new RequestOptions(1000, 1),
    ) {
    }
}
