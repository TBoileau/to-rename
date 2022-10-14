<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Doctrine\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class CategoryCrudController extends AbstractCrudController
{
    public function __construct(private readonly AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Catégorie')
            ->setEntityLabelInPlural('Catégories')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(
                Crud::PAGE_INDEX,
                Action::new(
                    'content',
                    'Ajouter un contenu'
                )->linkToUrl(
                    fn (Category $category): string => $this->adminUrlGenerator
                        ->setController(ContentCrudController::class)
                        ->setAction(Action::NEW)
                        ->set('category', $category->getId())
                        ->generateUrl()
                )
            )
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nom');
        yield TextareaField::new('description', 'Description')
            ->hideOnIndex();
        yield ImageField::new('image', 'Image')
            ->setUploadDir('public/uploads')
            ->setBasePath('/uploads');
        yield CodeEditorField::new('template', 'Template')
            ->setLanguage('twig')
            ->hideOnIndex();
        yield CollectionField::new('parameters', 'Paramètres')
            ->setEntryType(TextType::class)
            ->hideOnIndex();
    }
}
