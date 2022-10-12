<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Doctrine\Entity\Challenge;
use App\Doctrine\Entity\ChallengeRule;
use App\EasyAdmin\Field\DurationField;
use App\Form\ChallengeRuleType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
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
        $manage = Action::new('manage', 'Gérer')
            ->linkToRoute('admin_challenge_manage', static fn (Challenge $challenge): array => ['id' => $challenge->getId()]);

        return $actions
            ->add(Crud::PAGE_DETAIL, $manage)
            ->add(Crud::PAGE_INDEX, $manage)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield UrlField::new('repository', 'Repository');
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
        yield IntegerField::new('totalPoints', 'Points gagnés')->hideOnForm();
        yield IntegerField::new('finalPoints', 'Points total')->hideOnForm();
    }

    #[Route('/admin/challenges/{id}/manage', name: 'admin_challenge_manage')]
    public function manage(Challenge $challenge): Response
    {
        return $this->render('admin/challenge/manage.html.twig', [
            'challenge' => $challenge,
        ]);
    }

    #[Route('/admin/challenges/{id}/start', name: 'admin_challenge_start')]
    public function start(
        Challenge $challenge,
        AdminUrlGenerator $adminUrlGenerator,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        $challenge->setStartedAt(new DateTimeImmutable());
        $entityManager->flush();

        return $this->redirect(
            $adminUrlGenerator
                ->setRoute('admin_challenge_manage', ['id' => $challenge->getId()])
                ->generateUrl()
        );
    }

    #[Route('/admin/challenges/{id}/finish', name: 'admin_challenge_finish')]
    public function finish(
        Challenge $challenge,
        AdminUrlGenerator $adminUrlGenerator,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        $challenge->setEndedAt(new DateTimeImmutable());
        $entityManager->flush();

        return $this->redirect(
            $adminUrlGenerator
                ->setRoute('admin_challenge_manage', ['id' => $challenge->getId()])
                ->generateUrl()
        );
    }

    #[Route('/admin/challenges/{id}/hit/{count}', name: 'admin_challenge_hit')]
    public function hit(
        int $count,
        ChallengeRule $challengeRule,
        AdminUrlGenerator $adminUrlGenerator,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        $challengeRule->hit($count);
        $entityManager->flush();

        /** @var Challenge $challenge */
        $challenge = $challengeRule->getChallenge();

        return $this->redirect(
            $adminUrlGenerator
                ->setRoute('admin_challenge_manage', ['id' => $challenge->getId()])
                ->generateUrl()
        );
    }
}
