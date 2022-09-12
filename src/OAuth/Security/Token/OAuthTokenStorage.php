<?php

declare(strict_types=1);

namespace App\OAuth\Security\Token;

use App\OAuth\ClientInterface;
use Traversable;

final class OAuthTokenStorage implements TokenStorageInterface
{
    /**
     * @var array<string, ClientInterface>
     */
    private array $clients;

    /**
     * @param Traversable<string, ClientInterface> $clients
     */
    public function __construct(Traversable $clients)
    {
        $this->clients = iterator_to_array($clients);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->clients[$offset]);
    }

    public function offsetGet(mixed $offset): TokenInterface
    {
        return $this->clients[$offset]->getToken();
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}
