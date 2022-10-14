<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Doctrine\Entity\Post;
use App\EasyAdmin\Field\StateField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

final class PostCrudController extends AbstractCrudController
{
    public function __construct(private readonly WorkflowInterface $postStateMachine)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Article')
            ->setEntityLabelInPlural('Articles')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setFormThemes(['admin/form.html.twig', '@EasyAdmin/crud/form_theme.html.twig']);
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addJsFile(Asset::new('https://unpkg.com/showdown/dist/showdown.min.js')->onlyOnForms())
            ->addJsFile(Asset::new('https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js')->onlyOnForms())
            ->addCssFile(Asset::new('https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/default.min.css')->onlyOnForms())
            ->addCssFile(Asset::new('https://unpkg.com/highlight.js@11.6.0/styles/atom-one-dark.css')->onlyOnForms())
            ->addJsFile(Asset::new('js/markdown.js')->defer()->onlyOnForms());
    }

    public function configureFilters(Filters $filters): Filters
    {
        $places = $this->postStateMachine->getDefinition()->getPlaces();

        $choices = [];

        foreach ($places as $place) {
            $choices[$this->postStateMachine->getMetadataStore()->getPlaceMetadata($place)['label']] = $place;
        }

        return $filters
            ->add(ChoiceFilter::new('state', 'Status')->setChoices($choices))
            ->add('createdAt')
            ->add('publishedAt');
    }

    public function configureActions(Actions $actions): Actions
    {
        $transitions = $this->postStateMachine->getDefinition()->getTransitions();
        foreach ($transitions as $transition) {
            $action = Action::new(
                $transition->getName(),
                $this->postStateMachine->getMetadataStore()->getTransitionMetadata($transition)['label']
            )
                ->displayIf(fn (Post $post) => $this->postStateMachine->can($post, $transition->getName()))
                ->linkToRoute(
                    'admin_post_transition',
                    fn (Post $post) => [
                        'id' => $post->getId(),
                        'transition' => $transition->getName(),
                    ]
                );
            $actions
                ->add(Crud::PAGE_DETAIL, $action)
                ->add(Crud::PAGE_INDEX, $action);
        }

        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield ImageField::new('cover', 'Couverture')
            ->setUploadDir('public/uploads')
            ->setBasePath('/uploads');
        yield TextField::new('title', 'Titre');
        yield SlugField::new('slug', 'Slug')->setTargetFieldName('title');
        yield StateField::new('state', 'État')
            ->setWorkflowName('post')
            ->hideOnForm();
        yield DateTimeField::new('createdAt', 'Créé le')->onlyOnDetail();
        yield DateTimeField::new('publishedAt', 'Publiée le')->hideOnForm();
        yield CodeEditorField::new('content', 'Contenu')
            ->setLanguage('markdown')
            ->setNumOfRows(20)
            ->setFormTypeOption('block_name', 'markdown')
            ->hideOnIndex();
    }

    #[Route('/admin/posts/{id}/transition/{transition}', name: 'admin_post_transition')]
    public function transition(
        Post $post,
        string $transition,
        EntityManagerInterface $entityManager,
        AdminUrlGenerator $adminUrlGenerator
    ): Response {
        $this->postStateMachine->apply($post, $transition);
        $entityManager->flush();
        $this->addFlash('success', 'L\'article a bien été mis à jour.');

        return $this->redirect(
            $adminUrlGenerator
                ->setController(PostCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($post->getId())
                ->generateUrl()
        );
    }
}
