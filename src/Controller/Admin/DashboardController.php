<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Live;
use App\Entity\Logo;
use App\Entity\Planning;
use App\Entity\User;
use App\Entity\Video;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DashboardController extends AbstractDashboardController
{
    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Twitch')->disableDarkMode();
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/admin.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Videos', 'fab fa-youtube', Video::class);
        yield MenuItem::linkToCrud('Logos', 'fa fa-image', Logo::class);
        yield MenuItem::linkToCrud('Planning', 'fa fa-calendar', Planning::class);
        yield MenuItem::linkToCrud('Lives', 'fa fa-video-camera', Live::class);
    }
}