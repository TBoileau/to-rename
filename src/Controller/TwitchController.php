<?php

declare(strict_types=1);

namespace App\Controller;

use App\Doctrine\Entity\Planning;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/twitch/{id}', name: 'twitch', requirements: ['id' => '\d+'], methods: [Request::METHOD_GET])]
final class TwitchController extends AbstractController
{
    public function __invoke(Planning $planning): Response
    {
        return $this->render('twitch.html.twig', ['planning' => $planning]);
    }
}
