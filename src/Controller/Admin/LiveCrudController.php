<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Live;
use App\SocialNetwork\SocialNetworkInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

final class LiveCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Live::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('planning')->add('livedAt');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Live')
            ->setEntityLabelInPlural('Lives')
            ->setDefaultSort(['livedAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $communicate = Action::new('communicate', 'Communiquer')
            ->linkToRoute('admin_live_communicate', static fn (Live $live): array => ['id' => $live->getId()]);

        return $actions
            ->add(Crud::PAGE_INDEX, $communicate)
            ->add(Crud::PAGE_DETAIL, $communicate)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextareaField::new('description', 'Description');
        yield AssociationField::new('planning', 'Planning');
        yield DateTimeField::new('livedAt', 'Date')
            ->setFormat('dd/MM/yyyy HH:mm');
    }

    #[Route('/admin/lives/{id}/communicate', name: 'admin_live_communicate')]
    public function communicate(
        Live $live,
        AdminUrlGenerator $adminUrlGenerator,
        SocialNetworkInterface $socialNetwork
    ): RedirectResponse {
        $socialNetwork->send(<<<EOF
Le live Twitch commence à {$live->getLivedAt()->format('H:i')} !

{$live->getDescription()} 

https://twitch.tv/toham
EOF
        );

        return new RedirectResponse(
            $adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($live->getId())
                ->generateUrl()
        );
    }
}
