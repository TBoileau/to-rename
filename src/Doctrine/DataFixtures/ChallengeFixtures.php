<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Challenge;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ChallengeFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [RuleFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $manager->persist(self::createChallenge($i));
        }

        $manager->flush();
    }

    private static function createChallenge(int $i): Challenge
    {
        $challenge = new Challenge();
        $challenge->setName(sprintf('Challenge %d', $i));
        $challenge->setDescription(sprintf('Description %d', $i));

        return $challenge;
    }
}
