<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Planning;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/twitch/{id}', name: 'twitch')]
final class TwitchController extends AbstractController
{
    public function __invoke(Planning $planning): Response
    {
        return $this->render('twitch.html.twig', ['planning' => $planning]);
    }
}
