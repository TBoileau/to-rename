<?php

declare(strict_types=1);

namespace App\Controller;

use App\OAuth\Security\Guard\AuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/twitter', name: 'twitter_')]
final class TwitterController extends AbstractController
{
    #[Route('/check', name: 'check')]
    public function check(Request $request, AuthenticatorInterface $twitterAuthenticator): RedirectResponse
    {
        $twitterAuthenticator->authenticate($request);

        if ($request->getSession()->has('referer')) {
            /** @var string $redirectUri */
            $redirectUri = $request->getSession()->get('referer');
            $request->getSession()->remove('referer');

            return $this->redirect($redirectUri);
        }

        return $this->redirectToRoute('admin');
    }

    #[Route('/auth', name: 'auth')]
    public function auth(Request $request, AuthenticatorInterface $twitterAuthenticator): RedirectResponse
    {
        $request->getSession()->set('referer', $request->headers->get('referer'));

        return $twitterAuthenticator->authorize();
    }
}
