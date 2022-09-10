<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Video;
use App\Google\Security\Token\TokenInterface;
use App\Google\Youtube\VideoSynchronizerInterface;
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
    public function __construct(private TokenInterface $googleToken, private string $uploadDir)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Video::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!$this->googleToken->isAuthenticated()) {
            $actions->disable(Action::EDIT);
        }

        $syncAll = Action::new('syncAll', 'Synchroniser toutes les vidéos')
            ->createAsGlobalAction()
            ->displayIf(fn () => $this->googleToken->isAuthenticated())
            ->linkToRoute('admin_video_sync_all');

        $syncOne = Action::new('syncOne', 'Synchroniser')
            ->displayIf(fn () => $this->googleToken->isAuthenticated())
            ->linkToRoute('admin_video_sync_one', static fn (Video $video): array => ['id' => $video->getId()]);

        return $actions
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX, $syncOne)
            ->add(Crud::PAGE_DETAIL, $syncOne)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncAll);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('youtubeId', 'Video')
            ->setTemplatePath('admin/field/video_youtube_id.html.twig')
            ->hideOnForm();
        yield TextField::new('youtubeId', 'Youtube ID')
            ->hideOnIndex()
            ->hideOnForm();
        yield ImageField::new('thumbnails[high]', 'Thumbnail Youtube')->hideOnForm();
        yield ImageField::new('thumbnail', 'Thumbnail')
            ->setBasePath('uploads/')
            ->setUploadDir($this->uploadDir)
            ->hideOnForm();
        yield IntegerField::new('season', 'Saison N°');
        yield IntegerField::new('episode', 'Episode N°');
        yield TextField::new('title', 'Titre');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield CollectionField::new('tags', 'Tags')
            ->setEntryType(TextType::class)
            ->setTemplatePath('admin/field/video_tags.html.twig');
        yield AssociationField::new('live', 'Live');
        yield AssociationField::new('logo', 'Logo');
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
}
