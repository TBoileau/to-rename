<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ContentImage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class ContentImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContentImage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Image')
            ->setEntityLabelInPlural('Images');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nom')->hideOnForm();
        yield ImageField::new('image', 'Image')
            ->setBasePath('uploads/')
            ->setUploadDir('/public/uploads/');
    }
}
