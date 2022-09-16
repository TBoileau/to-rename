<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Live;
use App\OAuth\Api\Twitter\TwitterClient;
use App\OAuth\Security\Token\OAuthToken;
use App\OAuth\Security\Token\TokenStorageInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Annotation\Route;

final class LiveCrudController extends AbstractCrudController
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Live::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('planning')->add('livedAt');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Live')
            ->setEntityLabelInPlural('Lives')
            ->setDefaultSort(['livedAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        /** @var OAuthToken $twitterToken */
        $twitterToken = $this->tokenStorage['twitter'];

        if (!$twitterToken->isAuthenticated()) {
            $actions->disable('tweet');
        }

        $tweet = Action::new('tweet', 'Tweet')
            ->linkToRoute('admin_live_tweet', static fn (Live $live): array => ['id' => $live->getId()]);

        $discord = Action::new('discord', 'Discord')
            ->linkToRoute('admin_live_discord', static fn (Live $live): array => ['id' => $live->getId()]);

        return $actions
            ->add(Crud::PAGE_INDEX, $tweet)
            ->add(Crud::PAGE_DETAIL, $tweet)
            ->add(Crud::PAGE_INDEX, $discord)
            ->add(Crud::PAGE_DETAIL, $discord)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextareaField::new('description', 'Description');
        yield AssociationField::new('planning', 'Planning');
        yield DateTimeField::new('livedAt', 'Date')
            ->setFormat('dd/MM/yyyy HH:mm');
    }

    #[Route('/admin/lives/{id}/tweet', name: 'admin_lives_tweet')]
    public function tweet(Live $live, AdminUrlGenerator $adminUrlGenerator, TwitterClient $twitterClient): RedirectResponse
    {
        $twitterClient->tweet(<<<EOF
Le live Twitch commence à {$live->getLivedAt()->format('H:i')} !

{$live->getDescription()} 

https://twitch.tv/toham
EOF
        );

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($live->getId())
                ->generateUrl()
        );
    }

    #[Route('/admin/lives/{id}/discord', name: 'admin_live_discord')]
    public function discord(Live $live, AdminUrlGenerator $adminUrlGenerator, ChatterInterface $chatter): RedirectResponse
    {
        $chatter->send((new ChatMessage(<<<EOF
@everyone

Le live Twitch commence à {$live->getLivedAt()->format('H:i')} !

{$live->getDescription()} 

https://twitch.tv/toham
EOF
        ))->transport('discord'));

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($live->getId())
                ->generateUrl()
        );
    }
}
