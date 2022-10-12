<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Doctrine\Entity\Token;
use App\Doctrine\Repository\TokenRepository;
use App\OAuth\ClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class OAuthSubscriber implements EventSubscriberInterface
{
    /**
     * @param iterable<string, ClientInterface> $clients
     */
    public function __construct(
        private iterable $clients,
        private TokenRepository $tokenRepository,
        private EntityManagerInterface $entityManager
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
        $request = $event->getRequest();

        foreach ($this->clients as $client) {
            if ($request->getSession()->has($client::getSessionKey())) {
                /** @var array{access_token: string, created: int, expires_in: int, refresh_token: string} $accessToken */
                $accessToken = $request->getSession()->get($client::getSessionKey());

                $client->setAccessToken($accessToken);

                /** @var Token $googleToken */
                $googleToken = $this->tokenRepository->findOneBy(['name' => 'google']);

                if ($client->isAccessTokenExpired() && null !== $googleToken->getRefreshToken()) {
                    /** @var array{access_token?: string, created?: int, expires_in?: int, refresh_token?: string} $accessToken */
                    $accessToken = $client->fetchAccessTokenWithRefreshToken($googleToken->getRefreshToken());

                    if (isset($accessToken['access_token']) && isset($accessToken['refresh_token'])) {
                        $googleToken->setRefreshToken($accessToken['refresh_token']);
                        $this->entityManager->flush();
                        $client->setAccessToken($accessToken);
                        $request->getSession()->set($client::getSessionKey(), $accessToken['access_token']);
                    }
                }
            }
        }
    }
}
