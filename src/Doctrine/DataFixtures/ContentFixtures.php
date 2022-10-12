<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Category;
use App\Doctrine\Entity\Content;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ContentFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class, ChallengeFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $contents = [
            ...$this->createChallenges(5),
            ...$this->createCapsules(5),
            ...$this->createGettingStarted(5),
            ...$this->createProjects(5),
            ...$this->createCodeReviews(5),
            ...$this->createKatas(5),
            ...$this->createPodcast(5),
        ];

        foreach ($contents as $content) {
            $manager->persist($content);
        }

        $manager->flush();
    }

    /**
     * @return iterable<array-key, Content>
     */
    private function createChallenges(int $count): iterable
    {
        for ($index = 1; $index <= $count; ++$index) {
            $content = new Content();
            $content->setTitle(sprintf('Challenge %d', $index));
            $content->setDescription(sprintf('Description %d', $index));
            /** @var Category $category */
            $category = $this->getReference('category.challenge');
            $content->setCategory($category);

            $content->setParameters([
                [
                    'name' => 'repository',
                    'value' => 'https://github.com/TBoileau',
                ],
                [
                    'name' => 'tags',
                    'value' => 'php, symfony',
                ],
            ]);

            yield $content;
        }
    }

    /**
     * @return iterable<array-key, Content>
     */
    private function createCapsules(int $count): iterable
    {
        for ($index = 1; $index <= $count; ++$index) {
            $content = new Content();
            $content->setTitle(sprintf('Capsule %d', $index));
            $content->setDescription(sprintf('Description %d', $index));
            /** @var Category $category */
            $category = $this->getReference('category.capsule');
            $content->setCategory($category);
            $content->setParameters([
                [
                    'name' => 'repository',
                    'value' => 'https://github.com/TBoileau',
                ],
                [
                    'name' => 'tags',
                    'value' => 'php, symfony',
                ],
            ]);

            yield $content;
        }
    }

    /**
     * @return iterable<array-key, Content>
     */
    private function createGettingStarted(int $count): iterable
    {
        for ($index = 1; $index <= $count; ++$index) {
            $content = new Content();
            $content->setTitle(sprintf('Getting started %d', $index));
            $content->setDescription(sprintf('Description %d', $index));
            /** @var Category $category */
            $category = $this->getReference('category.getting_started');
            $content->setCategory($category);
            $content->setParameters([
                [
                    'name' => 'repository',
                    'value' => 'https://github.com/TBoileau',
                ],
                [
                    'name' => 'type',
                    'value' => 'framework',
                ],
                [
                    'name' => 'technology',
                    'value' => 'https://symfony.com/',
                ],
                [
                    'name' => 'tags',
                    'value' => 'php, symfony',
                ],
            ]);

            yield $content;
        }
    }

    /**
     * @return iterable<array-key, Content>
     */
    private function createCodeReviews(int $count): iterable
    {
        for ($index = 1; $index <= $count; ++$index) {
            $content = new Content();
            $content->setTitle(sprintf('Code review %d', $index));
            $content->setDescription(sprintf('Description %d', $index));
            /** @var Category $category */
            $category = $this->getReference('category.code_review');
            $content->setCategory($category);
            $content->setParameters([
                [
                    'name' => 'repository',
                    'value' => 'https://github.com/TBoileau',
                ],
                [
                    'name' => 'author',
                    'value' => 'Jane Doe',
                ],
                [
                    'name' => 'tags',
                    'value' => 'php, symfony',
                ],
            ]);

            yield $content;
        }
    }

    /**
     * @return iterable<array-key, Content>
     */
    private function createProjects(int $count): iterable
    {
        for ($index = 1; $index <= $count; ++$index) {
            $content = new Content();
            $content->setTitle(sprintf('Projet %d', $index));
            $content->setDescription(sprintf('Description %d', $index));
            /** @var Category $category */
            $category = $this->getReference('category.project');
            $content->setCategory($category);
            $content->setParameters([
                [
                    'name' => 'repository',
                    'value' => 'https://github.com/TBoileau',
                ],
                [
                    'name' => 'professional',
                    'value' => 0 === $index % 2 ? 'Oui' : 'Non',
                ],
                [
                    'name' => 'tags',
                    'value' => 'php, symfony',
                ],
            ]);

            yield $content;
        }
    }

    /**
     * @return iterable<array-key, Content>
     */
    private function createKatas(int $count): iterable
    {
        for ($index = 1; $index <= $count; ++$index) {
            $content = new Content();
            $content->setTitle(sprintf('Kata %d', $index));
            $content->setDescription(sprintf('Description %d', $index));
            /** @var Category $category */
            $category = $this->getReference('category.kata');
            $content->setCategory($category);
            $content->setParameters([
                [
                    'name' => 'repository',
                    'value' => 'https://github.com/TBoileau',
                ],
                [
                    'name' => 'type',
                    'value' => 'Algorithmie',
                ],
                [
                    'name' => 'tags',
                    'value' => 'php, symfony',
                ],
            ]);

            yield $content;
        }
    }

    /**
     * @return iterable<array-key, Content>
     */
    private function createPodcast(int $count): iterable
    {
        for ($index = 1; $index <= $count; ++$index) {
            $content = new Content();
            $content->setTitle(sprintf('Podcast %d', $index));
            $content->setDescription(sprintf('Description %d', $index));
            /** @var Category $category */
            $category = $this->getReference('category.podcast');
            $content->setCategory($category);
            $content->setParameters([
                [
                    'name' => 'guests',
                    'value' => 'John Doe, Jane Doe',
                ],
                [
                    'name' => 'tags',
                    'value' => 'php, symfony',
                ],
            ]);

            yield $content;
        }
    }
}
