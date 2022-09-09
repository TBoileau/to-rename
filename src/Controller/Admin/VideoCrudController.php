<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Video;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

final class VideoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Video::class;
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
        yield ImageField::new('logo', 'Logo')
            ->setBasePath('uploads/')
            ->setUploadDir('public/uploads/');
    }
}
