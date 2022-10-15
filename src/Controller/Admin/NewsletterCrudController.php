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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

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
            ->showEntityActionsInlined()
            ->setDefaultSort(['scheduledAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('scheduledAt', 'Programmé le');
        yield AssociationField::new('posts', 'Articles');
        yield AssociationField::new('lives', 'Lives');
        yield IntegerField::new('campaignId', 'ID de la campagne')->onlyOnDetail();
        yield IntegerField::new('complaints', 'Nombre de plaintes')->onlyOnDetail();
        yield IntegerField::new('sent', 'Envoyé')->onlyOnDetail();
        yield IntegerField::new('delivered', 'Délivrés')->onlyOnDetail();
        yield IntegerField::new('unsubscriptions', 'Désinscriptions')->onlyOnDetail();
        yield IntegerField::new('uniqueViews', 'Vues uniques')->onlyOnDetail();
        yield IntegerField::new('viewed', 'Total ouvertures')->onlyOnDetail();
        yield IntegerField::new('trackableViews', 'Ouvertures traçables')->onlyOnDetail();
        yield IntegerField::new('uniqueClick', 'Clics uniques')->onlyOnDetail();
        yield IntegerField::new('clickers', 'Total des clics')->onlyOnDetail();
        yield IntegerField::new('softBounces', 'Soft bounces')->onlyOnDetail();
        yield IntegerField::new('hardBounces', 'Hard bounces')->onlyOnDetail();
    }
}
