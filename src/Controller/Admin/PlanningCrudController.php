<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Planning;
use App\OAuth\Security\Token\OAuthToken;
use App\OAuth\Security\Token\TokenStorageInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Notifier\Bridge\Discord\DiscordOptions;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordEmbed;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordMediaEmbedObject;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
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

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('startedAt')->add('endedAt');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Planning')
            ->setEntityLabelInPlural('Plannings')
            ->setDefaultSort(['startedAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        /** @var OAuthToken $twitterToken */
        $twitterToken = $this->tokenStorage['twitter'];

        if (!$twitterToken->isAuthenticated()) {
            $actions->disable('tweet');
        }

        /** @var OAuthToken $linkedInToken */
        $linkedInToken = $this->tokenStorage['linkedin'];

        if (!$linkedInToken->isAuthenticated()) {
            $actions->disable('linkedin');
        }

        $tweet = Action::new('tweet', 'Tweet')
            ->linkToRoute('admin_planning_tweet', static fn (Planning $planning): array => ['id' => $planning->getId()]);

        $discord = Action::new('discord', 'Discord')
            ->linkToRoute('admin_planning_discord', static fn (Planning $planning): array => ['id' => $planning->getId()]);

        $linkedin = Action::new('linkedin', 'LinkedIn')
            ->linkToRoute('admin_planning_linkedin', static fn (Planning $planning): array => ['id' => $planning->getId()]);

        return $actions
            ->add(Crud::PAGE_INDEX, $tweet)
            ->add(Crud::PAGE_DETAIL, $tweet)
            ->add(Crud::PAGE_INDEX, $discord)
            ->add(Crud::PAGE_DETAIL, $discord)
            ->add(Crud::PAGE_INDEX, $linkedin)
            ->add(Crud::PAGE_DETAIL, $linkedin)
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
    public function tweet(int $id, AdminUrlGenerator $adminUrlGenerator, ChatterInterface $chatter): RedirectResponse
    {
        $chatter->send(
            (new ChatMessage(sprintf('https://toham.thomas-boileau.fr/twitch/%d', $id)))
                ->transport('twitter')
        );

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($id)
                ->generateUrl()
        );
    }

    #[Route('/admin/plannings/{id}/discord', name: 'admin_planning_discord')]
    public function discord(Planning $planning, AdminUrlGenerator $adminUrlGenerator, ChatterInterface $chatter): RedirectResponse
    {
        $discordEmbedObject = (new DiscordMediaEmbedObject())
            ->url(sprintf('https://toham.thomas-boileau.fr/uploads/%d', $planning->getImage()));

        $discordOptions = (new DiscordOptions())->addEmbed((new DiscordEmbed())->image($discordEmbedObject));

        $chatter->send((new ChatMessage(<<<EOF
@everyone

Planning de stream du {$planning->getStartedAt()->format('d/m/Y')} au {$planning->getEndedAt()->format('d/m/Y')}
EOF
        ))->options($discordOptions)->transport('discord'));

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($planning->getId())
                ->generateUrl()
        );
    }

    #[Route('/admin/plannings/{id}/linkedin', name: 'admin_planning_linkedin')]
    public function linkedin(Planning $planning, AdminUrlGenerator $adminUrlGenerator, ChatterInterface $chatter): RedirectResponse
    {
        $image = sprintf('https://toham.thomas-boileau.fr/twitch/%d', $planning->getId());

        $chatter->send((new ChatMessage(<<<EOF
Planning de stream du {$planning->getStartedAt()->format('d/m/Y')} au {$planning->getEndedAt()->format('d/m/Y')}

{$image}
EOF
        ))->transport('linkedin'));

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($planning->getId())
                ->generateUrl()
        );
    }
}
