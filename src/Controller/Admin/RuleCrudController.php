<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Doctrine\Entity\Rule;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class RuleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rule::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Règles')
            ->setEntityLabelInPlural('Règles')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nom');
        yield TextareaField::new('description', 'Description');
        yield IntegerField::new('points', 'Points');
    }
}
