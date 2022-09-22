<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Capsule;
use App\Entity\Challenge;
use App\Entity\CodeReview;
use App\Entity\GettingStarted;
use App\Entity\Kata;
use App\Entity\Live;
use App\Entity\Planning;
use App\Entity\Podcast;
use App\Entity\Project;
use App\Entity\Rule;
use App\Entity\User;
use App\Entity\Video;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Toham')
            ->setTranslationDomain('fr')
            ->setFaviconPath('images/favicon.ico')
            ->disableDarkMode();
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName('Toham')
            ->setAvatarUrl('images/Toham_Avatar.png');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/admin.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
        yield MenuItem::subMenu('Créateur de contenu', 'fa fa-video')->setSubItems([
            MenuItem::linkToCrud('Videos', 'fab fa-youtube', Video::class),
            MenuItem::linkToCrud('Planning', 'fa fa-calendar', Planning::class),
            MenuItem::linkToCrud('Lives', 'fab fa-twitch', Live::class),
        ]);
        yield MenuItem::subMenu('Gestion du contenu', 'fa fa-folder-tree')->setSubItems([
            MenuItem::linkToCrud('Capsules', 'fa fa-capsules', Capsule::class),
            MenuItem::linkToCrud('Code reviews', 'fa fa-magnifying-glass', CodeReview::class),
            MenuItem::linkToCrud('Défis', 'fa fa-chess', Challenge::class),
            MenuItem::linkToCrud('Règles', 'fa fa-scroll', Rule::class),
            MenuItem::linkToCrud('Getting started', 'fa fa-circle-play', GettingStarted::class),
            MenuItem::linkToCrud('Katas', 'fa fa-dumbbell', Kata::class),
            MenuItem::linkToCrud('Podcasts', 'fa fa-podcast', Podcast::class),
            MenuItem::linkToCrud('Projets', 'fa fa-diagram-project', Project::class),
        ]);
    }
}
