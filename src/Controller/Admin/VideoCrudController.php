<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\EasyAdmin\Field\StatusField;
use App\EasyAdmin\Filter\StatusFilter;
use App\Entity\Video;
use App\OAuth\ClientInterface;
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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotNull;

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
        if (!$this->googleClient->isAccessTokenExpired()) {
            $actions->disable(Action::EDIT, 'synchronize', 'statistics');
        }

        $statistics = Action::new('statistics', 'Statistiques')
            ->createAsGlobalAction()
            ->linkToRoute('admin_video_statistics');

        $synchronize = Action::new('synchronize', 'Synchroniser')
            ->createAsGlobalAction()
            ->linkToRoute('admin_video_synchronize');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $synchronize)
            ->add(Crud::PAGE_INDEX, $statistics);
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
        yield AssociationField::new('category', 'Catégorie')
            ->setFormTypeOption('constraints', [new NotNull()])
            ->hideWhenCreating();
        yield TextField::new('title', 'Titre')->hideWhenCreating();
        yield TextareaField::new('description', 'Description')
            ->hideWhenCreating()
            ->hideOnIndex();
        yield IntegerField::new('views', 'Vues')->hideOnForm();
        yield IntegerField::new('likes', 'Likes')->hideOnForm();
        yield IntegerField::new('comments', 'Commentaires')->hideOnForm();
        yield CollectionField::new('tags', 'Tags')
            ->setEntryType(TextType::class)
            ->setTemplatePath('admin/field/video_tags.html.twig')
            ->hideWhenCreating();
        yield AssociationField::new('live', 'Live')->hideWhenCreating();
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
