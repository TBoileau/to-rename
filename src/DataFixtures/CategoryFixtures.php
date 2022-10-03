<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Challenge;
use App\Entity\ParameterType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(
            $category = self::createCategory(
                name: 'Challenge',
                description: 'Concevoir un projet en un temps donné tout en respectant certaines règles.',
                image: 'challenge.png',
                template: <<<EOF
Format : content.getCategory().getName()
Description : content.getDescription()
Repository : content.getParameter('repository')
Règles : 
content.getParameter('challenge').getTextRules()
EOF,
                parameters: [
                    'repository' => ParameterType::Url,
                    'challenge' => ParameterType::Entity,
                ],
                targetEntity: Challenge::class
            )
        );

        $this->addReference('category.challenge', $category);

        $manager->persist(
            $gettingStarted = self::createCategory(
                name: 'Getting Started',
                description: 'Découverte d\'une nouvelle technologie, telle qu\'une librairie, un framework ou un langage de programmation.',
                image: 'getting_started.png',
                template: <<<EOF
Format : content.getCategory().getName()
Description : content.getDescription()
Type : content.getParameter('type')
Technologies : content.getParameter('technology')
Repository : content.getParameter('repository')
EOF,
                parameters: [
                    'repository' => ParameterType::Url,
                    'technology' => ParameterType::Url,
                    'type' => ParameterType::Choice,
                ],
                choices: [
                    'Librairie',
                    'Framework',
                    'Langage de programmation',
                ],
            )
        );

        $this->addReference('category.getting_started', $gettingStarted);

        $manager->persist(
            $capsule = self::createCategory(
                name: 'Capsule',
                description: 'Une capsule est un format court qui permet de présenter un sujet précis dans le détail.',
                image: 'capsule.png',
                template: <<<EOF
Format : content.getCategory().getName()
Description : content.getDescription()
Repository : content.getParameter('repository')
EOF,
                parameters: [
                    'repository' => ParameterType::Url,
                ],
            )
        );

        $this->addReference('category.capsule', $capsule);

        $manager->persist(
            $codeReview = self::createCategory(
                name: 'Code review',
                description: 'La revue de code est une pratique qui permet d\'identifier les éléments que l\'on peut améliorer dans un projet.',
                image: 'code_review.png',
                template: <<<EOF
Format : content.getCategory().getName()
Description : content.getDescription()
Auteur : content.getParameter('author')
Repository : content.getParameter('repository')
EOF,
                parameters: [
                    'repository' => ParameterType::Url,
                    'author' => ParameterType::String,
                ]
            )
        );

        $this->addReference('category.code_review', $codeReview);

        $manager->persist(
            $project = self::createCategory(
                name: 'Projet',
                description: 'Conception d\'un projet, qu\'il soit personnel ou professionel.',
                image: 'project.png',
                template: <<<EOF
Format : content.getCategory().getName()
Description : content.getDescription()
Projet professionel : content.getParameter('professional') ? 'Oui' : 'Non'
Repository : content.getParameter('repository')
EOF,
                parameters: [
                    'repository' => ParameterType::Url,
                    'professional' => ParameterType::Boolean,
                ]
            )
        );

        $this->addReference('category.project', $project);

        $manager->persist(
            $podcast = self::createCategory(
                name: 'Podcast',
                description: 'Avec ou sans invité⋅es, parlons d\'un thème dans le monde de la tech.',
                image: 'podcast.png',
                template: <<<EOF
Format : content.getCategory().getName()
Description : content.getDescription()
Invité⋅es : implode(', ', content.getParameter('guests'))
EOF,
                parameters: [
                    'guests' => ParameterType::Array,
                ]
            )
        );

        $this->addReference('category.podcast', $podcast);

        $manager->persist(
            $kata = self::createCategory(
                name: 'Kata',
                description: 'Un kata est un exercice de programmation qui permet de s\'entraîner sur différents sujets, comme l\'algorithmie, le refactong, etc...',
                image: 'kata.png',
                template: <<<EOF
Format : content.getCategory().getName()
Description : content.getDescription()
Type : content.getParameter('type')
Repository : content.getParameter('repository')
EOF,
                parameters: [
                    'repository' => ParameterType::Url,
                    'type' => ParameterType::Choice,
                ],
                choices: [
                    'Algorithmie',
                    'Refactoring',
                    'Test Driven Development',
                    'Autre',
                ],
            )
        );

        $this->addReference('category.kata', $kata);

        $manager->flush();
    }

    /**
     * @param array<string, ParameterType> $parameters
     * @param array<array-key, string>     $choices
     * @param class-string|null            $targetEntity
     */
    private static function createCategory(
        string $name,
        string $description,
        string $image,
        string $template,
        array $parameters = [],
        array $choices = [],
        ?string $targetEntity = null
    ): Category {
        $category = new Category();
        $category->setName($name);
        $category->setDescription($description);
        $category->setImage($image);
        $category->setParameters($parameters);
        $category->setChoices($choices);
        $category->setTemplate($template);
        $category->setTargetEntity($targetEntity);

        return $category;
    }
}
