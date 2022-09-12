<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Live;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

final class LiveCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Live::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextareaField::new('description', 'Description');
        yield AssociationField::new('planning', 'Planning');
        yield DateTimeField::new('livedAt', 'Date')
            ->setFormat('dd/MM/yyyy HH:mm');
    }
}
