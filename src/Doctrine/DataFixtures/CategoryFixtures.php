<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Category;
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
                template: 'categories/challenge.txt.twig',
                parameters: ['repository', 'tags'],
            )
        );

        $this->addReference('category.challenge', $category);

        $manager->persist(
            $gettingStarted = self::createCategory(
                name: 'Getting Started',
                description: 'Découverte d\'une nouvelle technologie, telle qu\'une librairie, un framework ou un langage de programmation.',
                image: 'getting_started.png',
                template: 'categories/getting_started.txt.twig',
                parameters: ['repository', 'technology', 'type', 'tags'],
            )
        );

        $this->addReference('category.getting_started', $gettingStarted);

        $manager->persist(
            $capsule = self::createCategory(
                name: 'Capsule',
                description: 'Une capsule est un format court qui permet de présenter un sujet précis dans le détail.',
                image: 'capsule.png',
                template: 'categories/capsule.txt.twig',
                parameters: ['repository', 'tags'],
            )
        );

        $this->addReference('category.capsule', $capsule);

        $manager->persist(
            $codeReview = self::createCategory(
                name: 'Code review',
                description: 'La revue de code est une pratique qui permet d\'identifier les éléments que l\'on peut améliorer dans un projet.',
                image: 'code_review.png',
                template: 'categories/code_review.txt.twig',
                parameters: ['repository', 'author', 'tags']
            )
        );

        $this->addReference('category.code_review', $codeReview);

        $manager->persist(
            $project = self::createCategory(
                name: 'Projet',
                description: 'Conception d\'un projet, qu\'il soit personnel ou professionel.',
                image: 'project.png',
                template: 'categories/project.txt.twig',
                parameters: ['repository', 'professional', 'tags']
            )
        );

        $this->addReference('category.project', $project);

        $manager->persist(
            $podcast = self::createCategory(
                name: 'Podcast',
                description: 'Avec ou sans invité⋅es, parlons d\'un thème dans le monde de la tech.',
                image: 'podcast.png',
                template: 'categories/podcast.txt.twig',
                parameters: ['guests', 'tags']
            )
        );

        $this->addReference('category.podcast', $podcast);

        $manager->persist(
            $kata = self::createCategory(
                name: 'Kata',
                description: 'Un kata est un exercice de programmation qui permet de s\'entraîner sur différents sujets, comme l\'algorithmie, le refactong, etc...',
                image: 'kata.png',
                template: <<<EOF
Rediffusion du live de {{ live.livedAt|date('d/m/Y H:i') }}
Format : {{ live.content.category.name }}
Description : {{ live.content.description }}
Type : {{ live.content.getParameter('type') }}
Repository : {{ live.content.getParameter('repository') }}
EOF,
                parameters: ['repository', 'type', 'tags']
            )
        );

        $this->addReference('category.kata', $kata);

        $manager->flush();
    }

    /**
     * @param array<array-key, string> $parameters
     */
    private static function createCategory(
        string $name,
        string $description,
        string $image,
        string $template,
        array $parameters = [],
    ): Category {
        $category = new Category();
        $category->setName($name);
        $category->setDescription($description);
        $category->setImage($image);
        $category->setParameters($parameters);
        $category->setTemplate($template);

        return $category;
    }
}
