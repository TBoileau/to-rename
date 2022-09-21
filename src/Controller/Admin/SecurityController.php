<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/admin', name: 'admin_security_')]
final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'translation_domain' => 'admin',
            'page_title' => 'Toham',
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('admin'),
            'username_label' => 'Identifiant',
            'password_label' => 'Mot de passe',
            'sign_in_label' => 'Connexion',
            'username_parameter' => '_username',
            'password_parameter' => '_password',
            'remember_me_enabled' => false,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
    }
}
