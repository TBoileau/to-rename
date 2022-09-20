<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\EasyAdmin\Field\DurationField;
use App\Entity\Challenge;
use App\Form\ChallengeRuleType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

final class ChallengeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Challenge::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Défi')
            ->setEntityLabelInPlural('Défis')
            ->setDefaultSort(['startedAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('live', 'Live')->setRequired(false)->hideWhenCreating();
        yield AssociationField::new('video', 'Vidéo')->setRequired(false)->hideWhenCreating();
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield UrlField::new('repository', 'Repository')->setRequired(false)->hideWhenCreating();
        yield DurationField::new('duration', 'Durée')->setRequired(true);
        yield DateTimeField::new('startedAt', 'Date de début')->hideOnForm();
        yield DateTimeField::new('endedAt', 'Date de fin')->hideOnForm();
        yield BooleanField::new('succeed', 'Réussi ?')->renderAsSwitch(false)->hideOnForm();
        yield CollectionField::new('rules', 'Règles')
            ->setEntryIsComplex(true)
            ->setEntryType(ChallengeRuleType::class)
            ->setFormTypeOption('by_reference', false)
            ->allowAdd(true)
            ->allowDelete(true)
            ->setTemplatePath('admin/field/challenge_rules.html.twig')
            ->hideWhenCreating()
            ->hideOnIndex();
        yield IntegerField::new('basePoints', 'Points de base');
        yield IntegerField::new('totalPoints', 'Points total')->hideOnForm();
    }
}
