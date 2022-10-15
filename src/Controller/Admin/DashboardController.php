<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Doctrine\Entity\Category;
use App\Doctrine\Entity\Challenge;
use App\Doctrine\Entity\Command;
use App\Doctrine\Entity\Content;
use App\Doctrine\Entity\Live;
use App\Doctrine\Entity\Newsletter;
use App\Doctrine\Entity\Planning;
use App\Doctrine\Entity\Post;
use App\Doctrine\Entity\Rule;
use App\Doctrine\Entity\User;
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
        return Assets::new()
            ->addCssFile('css/admin.css');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Articles', 'fa fa-pen', Post::class);
        yield MenuItem::linkToCrud('Newsletters', 'fa fa-envelope', Newsletter::class);
        yield MenuItem::linkToCrud('Planning', 'fa fa-calendar', Planning::class);
        yield MenuItem::linkToCrud('Lives', 'fab fa-twitch', Live::class);
        yield MenuItem::linkToCrud('Commandes', 'fas fa-terminal', Command::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-tags', Category::class);
        yield MenuItem::linkToCrud('Contenu', 'fa fa-pen-to-square', Content::class);
        yield MenuItem::linkToCrud('Défis', 'fa fa-chess', Challenge::class);
        yield MenuItem::linkToCrud('Règles', 'fa fa-scroll', Rule::class);
    }
}
