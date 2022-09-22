<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\EasyAdmin\Field\StatusField;
use App\EasyAdmin\Filter\StatusFilter;
use App\Entity\Video;
use App\OAuth\ClientInterface;
use App\Video\VideoManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Component\Routing\Annotation\Route;

final class VideoCrudController extends AbstractCrudController
{
    public function __construct(private ClientInterface $googleClient)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Video::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('live')
            ->add(StatusFilter::new('status', 'Statut'));
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Vidéo')
            ->setEntityLabelInPlural('Vidéos')
            ->setDefaultSort(['id' => 'DESC'])
            ->setFormOptions(
                ['validation_groups' => ['Default', 'create']],
                ['validation_groups' => ['Default', 'update']]
            );
    }

    public function configureActions(Actions $actions): Actions
    {
        if ($this->googleClient->isAccessTokenExpired()) {
            $actions->disable(Action::EDIT, Action::NEW, 'synchronize', 'synchronizeOne', 'statistics');
        }

        $statistics = Action::new('statistics', 'Statistiques')
            ->createAsGlobalAction()
            ->linkToRoute('admin_video_statistics');

        $synchronize = Action::new('synchronize', 'Synchroniser')
            ->createAsGlobalAction()
            ->linkToRoute('admin_video_synchronize');

        $synchronizeOne = Action::new('synchronizeOne', 'Synchroniser')
            ->linkToRoute('admin_video_synchronize_one', static fn (Video $video): array => ['id' => $video->getId()]);

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $synchronize)
            ->add(Crud::PAGE_INDEX, $synchronizeOne)
            ->add(Crud::PAGE_DETAIL, $synchronizeOne)
            ->add(Crud::PAGE_INDEX, $statistics);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('youtubeId', 'Youtube ID')->onlyWhenCreating();
        yield ImageField::new('thumbnail', 'Thumbnail')
            ->setBasePath('uploads/')
            ->hideOnForm();
        yield StatusField::new('status', 'Statut')
            ->hideWhenCreating();
        yield TextField::new('videoTitle', 'Titre')
            ->hideOnForm();
        yield TextField::new('videoDescription', 'Description')
            ->hideOnIndex()
            ->hideOnForm();
        yield TextField::new('title', 'Titre')
            ->setRequired(false)
            ->onlyWhenUpdating()
            ->hideWhenCreating();
        yield TextareaField::new('description', 'Description')
            ->setRequired(false)
            ->onlyWhenUpdating();
        yield IntegerField::new('views', 'Vues')->hideOnForm();
        yield IntegerField::new('likes', 'Likes')->hideOnForm();
        yield IntegerField::new('comments', 'Commentaires')->hideOnForm();
        yield CollectionField::new('tags', 'Tags')
            ->setEntryType(TextType::class)
            ->setTemplatePath('admin/field/video_tags.html.twig')
            ->hideWhenCreating();
        yield AssociationField::new('live', 'Live')->hideWhenCreating();
        yield ImageField::new('logo', 'Logo')
            ->setBasePath('uploads/')
            ->setUploadDir('/public/uploads/')
            ->setRequired(false)
            ->hideWhenCreating();
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

    #[Route('/admin/videos/{id}/synchronize', name: 'admin_video_synchronize_one')]
    public function synchronizeOne(
        Video $video,
        EntityManagerInterface $entityManager,
        VideoManagerInterface $videoManager,
        AdminUrlGenerator $adminUrlGenerator
    ): RedirectResponse {
        $videoManager->hydrate($video);

        $entityManager->flush();

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($video->getId())
                ->generateUrl()
        );
    }

    #[Route('/admin/videos/admin_video_statistics', name: 'admin_video_statistics')]
    public function admin_video_statistics(VideoManagerInterface $videoManager, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $videoManager->updateStatistics();

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }
}
