<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Planning;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

final class PlanningCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Planning::class;
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
}
