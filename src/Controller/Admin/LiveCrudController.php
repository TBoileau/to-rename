<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Doctrine\Entity\Live;
use App\EasyAdmin\Field\DurationField;
use App\EasyAdmin\Field\StatusField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

final class LiveCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Live::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('season')
            ->add('episode')
            ->add('planning')
            ->add('content')
            ->add('livedAt');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Live')
            ->setEntityLabelInPlural('Lives')
            ->setDefaultSort(['season' => 'DESC', 'episode' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield ImageField::new('thumbnail', 'Thumbnail')
            ->setBasePath('uploads/')
            ->hideOnForm();
        yield IntegerField::new('season', 'Saison N°');
        yield IntegerField::new('episode', 'Episode N°');
        yield AssociationField::new('planning', 'Planning');
        yield AssociationField::new('content', 'Contenu');
        yield DateTimeField::new('livedAt', 'Date')
            ->setFormat('dd/MM/yyyy HH:mm');
        yield DurationField::new('duration', 'Durée')->setRequired(true);
        yield StatusField::new('video.status', 'Statut')->hideOnForm();
        yield IntegerField::new('video.views', 'Vues')->hideOnForm();
        yield IntegerField::new('video.likes', 'Likes')->hideOnForm();
        yield IntegerField::new('video.comments', 'Commentaires')->hideOnForm();
        yield TextField::new('youtubeId', 'Video')
            ->setTemplatePath('admin/field/video_youtube_id.html.twig');
    }
}
