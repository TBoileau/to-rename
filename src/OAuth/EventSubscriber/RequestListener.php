<?php

declare(strict_types=1);

namespace App\OAuth\EventSubscriber;

use App\OAuth\Security\Guard\AuthenticatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestListener implements EventSubscriberInterface
{
    /**
     * @param iterable<string, AuthenticatorInterface> $authenticators
     */
    public function __construct(private iterable $authenticators)
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
        foreach ($this->authenticators as $authenticator) {
            $authenticator->refresh($event->getRequest());
        }
    }
}
