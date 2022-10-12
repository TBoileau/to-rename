<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Doctrine\Entity\Category;
use App\Doctrine\Entity\Content;
use App\Doctrine\Repository\CategoryRepository;
use App\Form\ParameterType;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\CrudDto;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use EasyCorp\Bundle\EasyAdminBundle\Exception\InsufficientEntityPermissionException;
use EasyCorp\Bundle\EasyAdminBundle\Factory\EntityFactory;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

final class ContentCrudController extends AbstractCrudController
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Content::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Contenu')
            ->setEntityLabelInPlural('Contenu')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield AssociationField::new('category', 'Catégorie')->hideOnForm();
        yield CollectionField::new('parameters', 'Paramètres')
            ->setEntryType(ParameterType::class)
            ->renderExpanded(true)
            ->showEntryLabel(false)
            ->allowDelete(false)
            ->allowAdd(false)
            ->hideOnIndex();
    }

    public function new(AdminContext $context): Response|KeyValueStore
    {
        /** @var Category $category */
        $category = $this->categoryRepository->find($context->getRequest()->query->getInt('category'));

        $event = new BeforeCrudActionEvent($context);
        $this->container->get('event_dispatcher')->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }

        if (!$this->isGranted(Permission::EA_EXECUTE_ACTION, ['action' => Action::NEW, 'entity' => null])) {
            throw new ForbiddenActionException($context);
        }

        if (!$context->getEntity()->isAccessible()) {
            throw new InsufficientEntityPermissionException($context);
        }

        /** @var Content $entity */
        $entity = $this->createEntity($context->getEntity()->getFqcn());
        $entity->setCategory($category);

        $context->getEntity()->setInstance($entity);
        $this->container->get(EntityFactory::class)->processFields($context->getEntity(), FieldCollection::new($this->configureFields(Crud::PAGE_NEW)));

        /** @var CrudDto $crudDto */
        $crudDto = $context->getCrud();

        /** @var FieldCollection $fieldCollection */
        $fieldCollection = $context->getEntity()->getFields();

        $assetsDto = $this->getFieldAssets($fieldCollection);

        $crudDto->setFieldAssets($assetsDto);

        /** @var EntityFactory $entityFactory */
        $entityFactory = $this->container->get(EntityFactory::class);

        $entityFactory->processActions($context->getEntity(), $crudDto->getActionsConfig());

        $newForm = $this->createNewForm($context->getEntity(), $crudDto->getNewFormOptions(), $context);
        $newForm->handleRequest($context->getRequest());

        /** @var Content $entityInstance */
        $entityInstance = $newForm->getData();

        $context->getEntity()->setInstance($entityInstance);

        if ($newForm->isSubmitted() && $newForm->isValid()) {
            $this->processUploadedFiles($newForm);

            $event = new BeforeEntityPersistedEvent($entityInstance);

            /** @var EventDispatcherInterface $eventDispatcher */
            $eventDispatcher = $this->container->get('event_dispatcher');

            $eventDispatcher->dispatch($event);
            $entityInstance = $event->getEntityInstance();

            /** @var Registry $doctrine */
            $doctrine = $this->container->get('doctrine');

            /** @var class-string $fqcn */
            $fqcn = $context->getEntity()->getFqcn();

            /** @var EntityManagerInterface $entityManager */
            $entityManager = $doctrine->getManagerForClass($fqcn);

            $this->persistEntity($entityManager, $entityInstance);

            $eventDispatcher->dispatch(new AfterEntityPersistedEvent($entityInstance));
            $context->getEntity()->setInstance($entityInstance);

            return $this->getRedirectResponseAfterSave($context, Action::NEW);
        }

        $responseParameters = $this->configureResponseParameters(KeyValueStore::new([
            'pageName' => Crud::PAGE_NEW,
            'templateName' => 'crud/new',
            'entity' => $context->getEntity(),
            'new_form' => $newForm,
        ]));

        $event = new AfterCrudActionEvent($context, $responseParameters);
        $this->container->get('event_dispatcher')->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }

        return $responseParameters;
    }
}
