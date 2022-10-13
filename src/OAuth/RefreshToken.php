<?php

declare(strict_types=1);

namespace App\OAuth;

use App\Doctrine\Entity\Token;
use App\Doctrine\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class RefreshToken implements RefreshTokenInterface
{
    public function __construct(
        private TokenRepository $tokenRepository,
        private EntityManagerInterface $entityManager,
        private CacheInterface $cache
    ) {
    }

    public function refresh(ClientInterface $client): void
    {
        /** @var array{access_token: string, created: int, expires_in: int, refresh_token: string}|null $accessToken */
        $accessToken = $this->cache->get($client::getSessionKey(), function (ItemInterface $item) use ($client) {
            /** @var Token $token */
            $token = $this->tokenRepository->findOneBy(['name' => $client::getName()]);

            if (null === $token->getRefreshToken()) {
                return null;
            }

            /** @var array{access_token?: string, created?: int, expires_in: int, refresh_token?: string} $accessToken */
            $accessToken = $client->fetchAccessTokenWithRefreshToken($token->getRefreshToken());

            if (isset($accessToken['access_token']) && isset($accessToken['refresh_token'])) {
                if (!isset($accessToken['created'])) {
                    $accessToken['created'] = time();
                }
                $item->expiresAfter($accessToken['expires_in']);
                $token->setRefreshToken($accessToken['refresh_token']);
                $this->entityManager->flush();

                return $accessToken;
            }

            return null;
        });

        if (null !== $accessToken) {
            $client->setAccessToken($accessToken);
        }
    }
}
