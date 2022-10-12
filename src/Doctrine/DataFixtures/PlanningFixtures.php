<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Planning;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class PlanningFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $datePeriod = new DatePeriod(
            new DateTimeImmutable('2022-01-03'),
            new DateInterval('P7D'),
            new DateTimeImmutable('2022-02-20')
        );

        foreach ($datePeriod as $date) {
            $manager->persist($this->createPlanning($date));
        }

        $manager->flush();
    }

    private function createPlanning(DateTimeImmutable $startedAt): Planning
    {
        $planning = new Planning();
        $planning->setStartedAt($startedAt);

        return $planning;
    }
}
