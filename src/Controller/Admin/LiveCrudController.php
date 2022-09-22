<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\EasyAdmin\Field\DurationField;
use App\Entity\Live;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('season', 'Saison N°');
        yield IntegerField::new('episode', 'Episode N°');
        yield TextField::new('videoTitle', 'Titre')
            ->hideOnForm();
        yield TextField::new('videoDescription', 'Description')
            ->hideOnIndex()
            ->hideOnForm();
        yield AssociationField::new('planning', 'Planning');
        yield AssociationField::new('content', 'Contenu');
        yield DateTimeField::new('livedAt', 'Date')
            ->setFormat('dd/MM/yyyy HH:mm');
        yield DurationField::new('duration', 'Durée')->setRequired(true);
    }
}
