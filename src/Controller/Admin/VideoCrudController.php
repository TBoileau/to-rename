<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Video;
use App\Google\Security\Token\TokenInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

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
            $actions->disable(Action::NEW, Action::EDIT);
        }

        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield ImageField::new('thumbnail', 'Thumbnail')
            ->setBasePath('uploads/')
            ->hideOnForm();
        yield IntegerField::new('season', 'Saison N°');
        yield IntegerField::new('episode', 'Episode N°');
        yield TextField::new('title', 'Titre');
        yield UrlField::new('link', 'Vidéo Youtube');
        yield AssociationField::new('live', 'Live');
        yield AssociationField::new('logo', 'Logo');
    }
}
