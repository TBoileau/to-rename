<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\OAuth\ClientInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class OAuthSubscriber implements EventSubscriberInterface
{
    /**
     * @param iterable<string, ClientInterface> $clients
     */
    public function __construct(private iterable $clients)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(): void
    {
        foreach ($this->clients as $client) {
            $client->refresh();
        }
    }
}
