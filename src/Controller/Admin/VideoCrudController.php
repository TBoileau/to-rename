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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

final class VideoCrudController extends AbstractCrudController
{
    public function __construct(private TokenInterface $googleToken)
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

        $synchronize = Action::new('synchronize', 'Synchroniser')
            ->createAsGlobalAction()
            ->linkToRoute('admin_video_synchronize');

        return $actions
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $synchronize);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('youtubeId', 'Video')
            ->setTemplatePath('admin/field/video_youtube_id.html.twig')
            ->hideOnForm();
        yield TextField::new('youtubeId', 'Youtube ID')
            ->hideOnIndex()
            ->hideOnForm();
        yield ImageField::new('thumbnails[high]', 'Thumbnail')->hideOnForm();
        yield TextField::new('title', 'Titre');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield CollectionField::new('tags', 'Tags')
            ->setEntryType(TextType::class)
            ->setTemplatePath('admin/field/video_tags.html.twig');
        yield AssociationField::new('live', 'Live');
        yield AssociationField::new('logo', 'Logo');
    }

    #[Route('/admin/videos/synchronize', name: 'admin_video_synchronize')]
    public function synchronize(VideoSynchronizerInterface $videoSynchronizer, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $videoSynchronizer->synchronize();

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }
}
