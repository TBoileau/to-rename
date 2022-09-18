<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Planning;
use App\SocialNetwork\SocialNetworkInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

final class PlanningCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Planning::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('startedAt')->add('endedAt');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Planning')
            ->setEntityLabelInPlural('Plannings')
            ->setDefaultSort(['startedAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $communicate = Action::new('communicate', 'Communiquer')
            ->linkToRoute('admin_planning_communicate', static fn (Planning $planning): array => ['id' => $planning->getId()]);

        return $actions
            ->add(Crud::PAGE_INDEX, $communicate)
            ->add(Crud::PAGE_DETAIL, $communicate)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield ImageField::new('image', 'Image')
            ->setBasePath('uploads/')
            ->hideOnForm();
        yield DateField::new('startedAt', 'Date de début')
            ->setFormat('dd/MM/yyyy');
        yield DateField::new('endedAt', 'Date de fin')
            ->setFormat('dd/MM/yyyy')
            ->hideOnForm();
    }

    #[Route('/admin/plannings/{id}/communicate', name: 'admin_planning_communicate')]
    public function communicate(
        Planning $planning,
        AdminUrlGenerator $adminUrlGenerator,
        SocialNetworkInterface $socialNetwork
    ): RedirectResponse {
        $image = sprintf('https://toham.thomas-boileau.fr/uploads/%d', $planning->getImage());

        $link = sprintf('https://toham.thomas-boileau.fr/twitch/%d', $planning->getId());

        $socialNetwork->send(<<<EOF
Planning de stream du {$planning->getStartedAt()->format('d/m/Y')} au {$planning->getEndedAt()->format('d/m/Y')}

{$link}
EOF
            , $image);

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($planning->getId())
                ->generateUrl()
        );
    }
}
