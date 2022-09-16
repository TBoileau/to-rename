<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cgu', name: 'cgu')]
final class CGUController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('cgu.html.twig');
    }
}
