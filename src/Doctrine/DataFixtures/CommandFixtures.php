<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Category;
use App\Doctrine\Entity\Command;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class CommandFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<array-key, Category> $categories */
        $categories = $manager->getRepository(Category::class)->findAll();

        foreach ($categories as $category) {
            $command = new Command();
            $command->setName('!test');
            $command->setCategory($category);
            $command->setTemplate('{{ live.content.description }}');
            $manager->persist($command);
        }

        $manager->flush();
    }
}
