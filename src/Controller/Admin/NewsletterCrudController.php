<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Doctrine\Entity\Newsletter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

final class NewsletterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Newsletter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Newsletter')
            ->setEntityLabelInPlural('Newsletters')
            ->setDefaultSort(['scheduledAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('scheduledAt', 'Programmé le');
        yield AssociationField::new('posts', 'Articles');
        yield AssociationField::new('lives', 'Lives');
    }
}
