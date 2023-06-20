<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper;

/**
 * @template-implements \IteratorAggregate<AuthScheme>
 */
final class RequestOptions implements \IteratorAggregate
{
    /** @var AuthScheme[] */
    public readonly array $authSchemes;

    /**
     * @param positive-int $timeout In milliseconds.
     * @param AuthScheme   ...$authSchemes
     */
    public function __construct(
        public readonly int $timeout,
        AuthScheme ...$authSchemes,
    ) {
        $this->authSchemes = $authSchemes;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        yield from $this->authSchemes;
    }
}
