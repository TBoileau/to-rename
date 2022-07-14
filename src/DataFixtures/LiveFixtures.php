<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Live;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Generator;

class LiveFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $start = new DateTimeImmutable('first Monday of january 2022');

        foreach (self::createLives($start, 4) as $live) {
            $manager->persist($live);
        }

        $start = $start->add(new DateInterval('P1W'));

        foreach (self::createLives($start, 2) as $live) {
            $manager->persist($live);
        }

        $manager->flush();
    }

    /**
     * @return Generator<Live>
     */
    private static function createLives(DateTimeImmutable $start, int $numberOfLives): Generator
    {
        $end = $start->add(new DateInterval(sprintf('P%dD', $numberOfLives)));

        $dateRange = new DatePeriod($start, new DateInterval('P1D'), $end);

        foreach ($dateRange as $date) {
            $live = new Live();
            $live->setStartedAt($date);
            $live->setDescription('Live on '.$date->format('l jS F Y'));
            yield $live;
        }
    }
}
