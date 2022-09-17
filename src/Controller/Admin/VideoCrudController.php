<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\EasyAdmin\Field\StatusField;
use App\EasyAdmin\Filter\StatusFilter;
use App\Entity\Video;
use App\OAuth\Api\Twitter\TwitterClient;
use App\OAuth\Security\Token\OAuthToken;
use App\OAuth\Security\Token\TokenStorageInterface;
use App\Video\VideoManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Annotation\Route;

use function Symfony\Component\String\u;

final class VideoCrudController extends AbstractCrudController
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Video::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('season')
            ->add('episode')
            ->add(StatusFilter::new('status', 'Statut'));
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Vidéo')
            ->setEntityLabelInPlural('Vidéos')
            ->setDefaultSort(['season' => 'DESC', 'episode' => 'DESC'])
            ->setFormOptions(
                ['validation_groups' => ['Default', 'create']],
                ['validation_groups' => ['Default', 'update']]
            );
    }

    public function configureActions(Actions $actions): Actions
    {
        /** @var OAuthToken $googleToken */
        $googleToken = $this->tokenStorage['google'];

        if (!$googleToken->isAuthenticated()) {
            $actions->disable(Action::EDIT, 'synchronize');
        }

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

        $synchronize = Action::new('synchronize', 'Synchroniser')
            ->createAsGlobalAction()
            ->linkToRoute('admin_video_synchronize');

        $tweet = Action::new('tweet', 'Tweet')
            ->linkToRoute('admin_video_tweet', static fn (Video $video): array => ['id' => $video->getId()]);

        $discord = Action::new('discord', 'Discord')
            ->linkToRoute('admin_video_discord', static fn (Video $video): array => ['id' => $video->getId()]);

        $linkedin = Action::new('linkedin', 'LinkedIn')
            ->linkToRoute('admin_video_linkedin', static fn (Video $video): array => ['id' => $video->getId()]);

        return $actions
            ->add(Crud::PAGE_INDEX, $tweet)
            ->add(Crud::PAGE_DETAIL, $tweet)
            ->add(Crud::PAGE_INDEX, $discord)
            ->add(Crud::PAGE_DETAIL, $discord)
            ->add(Crud::PAGE_INDEX, $linkedin)
            ->add(Crud::PAGE_DETAIL, $linkedin)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $synchronize);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('youtubeId', 'Youtube ID')->onlyWhenCreating();
        yield ImageField::new('thumbnail', 'Thumbnail')
            ->setBasePath('uploads/')
            ->hideOnForm();
        yield IntegerField::new('season', 'Saison N°')->hideWhenCreating();
        yield IntegerField::new('episode', 'Episode N°')->hideWhenCreating();
        yield StatusField::new('status', 'Statut')
            ->hideWhenCreating();
        yield TextField::new('title', 'Titre')->hideWhenCreating();
        yield TextareaField::new('description', 'Description')
            ->hideWhenCreating()
            ->hideOnIndex();
        yield CollectionField::new('tags', 'Tags')
            ->setEntryType(TextType::class)
            ->setTemplatePath('admin/field/video_tags.html.twig')
            ->hideWhenCreating();
        yield AssociationField::new('live', 'Live')->hideWhenCreating();
        yield AssociationField::new('logo', 'Logo')->hideWhenCreating();
        yield TextField::new('youtubeId', 'Video')
            ->setTemplatePath('admin/field/video_youtube_id.html.twig')
            ->hideOnForm();
    }

    #[Route('/admin/videos/synchronize', name: 'admin_video_synchronize')]
    public function synchronize(VideoManagerInterface $videoManager, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $videoManager->synchronize();

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }

    #[Route('/admin/videos/{id}/tweet', name: 'admin_video_tweet')]
    public function tweet(Video $video, AdminUrlGenerator $adminUrlGenerator, TwitterClient $twitterClient): RedirectResponse
    {
        $twitterClient->tweet(<<<EOF
Nouvelle vidéo disponible sur la chaîne Youtube ! 

{$video->getTitle()}

https://www.youtube.com/watch?v={$video->getYoutubeId()}
EOF
        );

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($video->getId())
                ->generateUrl()
        );
    }

    #[Route('/admin/videos/{id}/discord', name: 'admin_video_discord')]
    public function discord(Video $video, AdminUrlGenerator $adminUrlGenerator, ChatterInterface $chatter): RedirectResponse
    {
        $title = u($video->getTitle())->trim()->toString();

        $chatter->send((new ChatMessage(<<<EOF
@everyone

Nouvelle vidéo disponible sur la chaîne Youtube ! 

{$title}

https://www.youtube.com/watch?v={$video->getYoutubeId()}
EOF
        ))->transport('discord'));

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($video->getId())
                ->generateUrl()
        );
    }

    #[Route('/admin/videos/{id}/linkedin', name: 'admin_video_linkedin')]
    public function linkedin(Video $video, AdminUrlGenerator $adminUrlGenerator, ChatterInterface $chatter): RedirectResponse
    {
        $title = u($video->getTitle())->trim()->toString();

        $chatter->send((new ChatMessage(<<<EOF
Nouvelle vidéo disponible sur la chaîne Youtube ! 

{$title}

https://www.youtube.com/watch?v={$video->getYoutubeId()}
EOF
        ))->transport('linkedin'));

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($video->getId())
                ->generateUrl()
        );
    }
}
