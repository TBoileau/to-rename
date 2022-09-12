<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Planning;
use App\OAuth\Api\Twitter\TwitterClient;
use App\OAuth\Security\Token\OAuthToken;
use App\OAuth\Security\Token\TokenStorageInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

final class PlanningCrudController extends AbstractCrudController
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Planning::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        /** @var OAuthToken $twitterToken */
        $twitterToken = $this->tokenStorage['twitter'];

        if (!$twitterToken->isAuthenticated()) {
            $actions->disable('tweet');
        }

        $tweet = Action::new('tweet', 'Tweet')
            ->linkToRoute('admin_planning_tweet', static fn (Planning $planning): array => ['id' => $planning->getId()]);

        return $actions
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX, $tweet)
            ->add(Crud::PAGE_DETAIL, $tweet)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield ImageField::new('image', 'Image')
            ->setBasePath('uploads/')
            ->hideOnForm();
        yield DateField::new('startedAt', 'Date de début')
            ->setFormat('dd/MM/yyyy');
        yield DateField::new('endedAt', 'Date de fin')
            ->setFormat('dd/MM/yyyy')
            ->hideOnForm();
    }

    #[Route('/admin/plannings/{id}/tweet', name: 'admin_planning_tweet')]
    public function tweet(int $id, AdminUrlGenerator $adminUrlGenerator, TwitterClient $twitterClient): RedirectResponse
    {
        $twitterClient->tweet(sprintf('https://toham.thomas-boileau.fr/twitch/%d', $id));

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($id)
                ->generateUrl()
        );
    }
}
