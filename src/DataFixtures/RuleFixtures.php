<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Rule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class RuleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 30; ++$i) {
            $manager->persist(self::createRule($i));
        }
        $manager->flush();
    }

    public static function createRule(int $index): Rule
    {
        $rule = new Rule();
        $rule->setName(sprintf('Rule %d', $index));
        $rule->setDescription(sprintf('Description %d', $index));
        $rule->setPoints(rand(1, 5));

        return $rule;
    }
}
