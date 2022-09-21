<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\OAuth\ClientInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
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

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        foreach ($this->clients as $client) {
            if ($request->getSession()->has($client::getSessionKey())) {
                /** @var array{access_token: string, created: int, expires_in: int, refresh_token: string} $accessToken */
                $accessToken = $request->getSession()->get($client::getSessionKey());

                $client->setAccessToken($accessToken);
            }
        }
    }
}
