<?php

declare(strict_types=1);

namespace App\Controller;

use App\Doctrine\Entity\Token;
use App\Doctrine\Repository\TokenRepository;
use App\OAuth\ClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/google', name: 'google_')]
final class GoogleController extends AbstractController
{
    #[Route('/check', name: 'check')]
    public function check(
        Request $request,
        ClientInterface $googleClient,
        TokenRepository $tokenRepository,
        EntityManagerInterface $entityManager,
        CacheInterface $cache
    ): RedirectResponse {
        /** @var string $code */
        $code = $request->get('code');

        /** @var array{access_token?: string, created?: int, expires_in?: int, refresh_token?: string} $accessToken */
        $accessToken = $googleClient->fetchAccessTokenWithAuthCode($code);

        if (!isset($accessToken['access_token']) || !isset($accessToken['refresh_token'])) {
            $this->addFlash('danger', 'Error lors de la connexion OAuth2 avec Google.');

            return $this->redirectToRoute('admin');
        }

        /** @var Token $googleToken */
        $googleToken = $tokenRepository->findOneBy(['name' => 'google']);

        $googleToken->setRefreshToken($accessToken['refresh_token']);

        $entityManager->flush();

        $cache->delete($googleClient::getSessionKey());

        $cache->get($googleClient::getSessionKey(), function (ItemInterface $item) use ($accessToken) {
            $item->expiresAfter(3);

            return $accessToken;
        });

        if ($request->getSession()->has('referer')) {
            /** @var string $redirectUri */
            $redirectUri = $request->getSession()->get('referer');
            $request->getSession()->remove('referer');

            return $this->redirect($redirectUri);
        }

        return $this->redirectToRoute('admin');
    }

    #[Route('/auth', name: 'auth')]
    public function auth(Request $request, ClientInterface $googleClient): RedirectResponse
    {
        $request->getSession()->set('referer', $request->headers->get('referer'));

        return new RedirectResponse($googleClient->createAuthUrl());
    }
}
