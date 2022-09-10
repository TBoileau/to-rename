<?php

declare(strict_types=1);

namespace App\Google\Http;

use App\Google\Security\Token\TokenInterface;
use Google_Client;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RequestListener implements EventSubscriberInterface
{
    public function __construct(
        private TokenInterface $token,
        private Google_Client $googleClient,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->googleClient->setRedirectUri(
            $this->urlGenerator->generate(
                'google_check',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );

        $session = $event->getRequest()->getSession();

        /** @var array<string, mixed>|null $token */
        $token = $session->get('google_access_token');

        if (null === $token) {
            return;
        }

        if (isset($token['error'])) {
            $session->remove('google_access_token');

            return;
        }

        $this->token->save($token);
    }
}
