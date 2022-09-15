<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Video;
use App\OAuth\Api\Twitter\TwitterClient;
use App\OAuth\Security\Token\OAuthToken;
use App\OAuth\Security\Token\TokenStorageInterface;
use App\Youtube\VideoSynchronizerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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
use Symfony\Component\Routing\Annotation\Route;

final class VideoCrudController extends AbstractCrudController
{
    public function __construct(private TokenStorageInterface $tokenStorage, private string $uploadDir)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Video::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
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
            $actions->disable(Action::EDIT, 'syncAll', 'syncOne');
        }

        /** @var OAuthToken $twitterToken */
        $twitterToken = $this->tokenStorage['twitter'];

        if (!$twitterToken->isAuthenticated()) {
            $actions->disable('tweet');
        }

        $syncAll = Action::new('syncAll', 'Synchroniser toutes les vidéos')
            ->createAsGlobalAction()
            ->linkToRoute('admin_video_sync_all');

        $syncOne = Action::new('syncOne', 'Synchroniser')
            ->linkToRoute('admin_video_sync_one', static fn (Video $video): array => ['id' => $video->getId()]);

        $tweet = Action::new('tweet', 'Tweet')
            ->linkToRoute('admin_video_tweet', static fn (Video $video): array => ['id' => $video->getId()]);

        return $actions
            ->add(Crud::PAGE_INDEX, $syncOne)
            ->add(Crud::PAGE_DETAIL, $syncOne)
            ->add(Crud::PAGE_INDEX, $tweet)
            ->add(Crud::PAGE_DETAIL, $tweet)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAll);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('youtubeId', 'Video')
            ->setTemplatePath('admin/field/video_youtube_id.html.twig')
            ->hideOnForm();
        yield TextField::new('youtubeId', 'Youtube ID')
            ->onlyWhenCreating()
            ->hideOnIndex();
        yield ImageField::new('thumbnails[maxres]', 'Thumbnail Youtube')->hideOnForm();
        yield ImageField::new('thumbnail', 'Thumbnail')
            ->setBasePath('uploads/')
            ->setUploadDir($this->uploadDir)
            ->hideOnForm();
        yield IntegerField::new('season', 'Saison N°')->hideWhenCreating();
        yield IntegerField::new('episode', 'Episode N°')->hideWhenCreating();
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
    }

    #[Route('/admin/videos/sync', name: 'admin_video_sync_all')]
    public function syncAll(VideoSynchronizerInterface $videoSynchronizer, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $videoSynchronizer->syncAll();

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }

    #[Route('/admin/videos/{id}/sync', name: 'admin_video_sync_one')]
    public function syncOne(Video $video, VideoSynchronizerInterface $videoSynchronizer, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $videoSynchronizer->syncOne($video);

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($video->getId())
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
}
