<?php

declare(strict_types=1);

namespace Kafkiansky\Zookeeper;

final class AuthScheme
{
    /**
     * @param non-empty-string $scheme In example "digest".
     * @param non-empty-string $credentials In "user:password" format.
     */
    public function __construct(
        public readonly string $scheme,
        public readonly string $credentials,
    ) {
    }
}
