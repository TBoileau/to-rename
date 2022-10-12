<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Content;
use App\Doctrine\Entity\Live;
use App\Doctrine\Entity\Planning;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LiveFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [ContentFixtures::class, PlanningFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<array-key, Planning> $plannings */
        $plannings = $manager->getRepository(Planning::class)->findAll();

        /** @var array<int<0, 35>, Content> $contents */
        $contents = $manager->getRepository(Content::class)->findAll();

        $index = 0;

        $season = 1;

        $episode = 1;

        foreach ($plannings as $planning) {
            $datePeriod = new DatePeriod(
                $planning->getStartedAt(),
                new DateInterval('P1D'),
                $planning->getStartedAt()->add(new DateInterval('P5D'))
            );

            /** @var DateTimeImmutable $date */
            foreach ($datePeriod as $date) {
                $live = new Live();
                $live->setLivedAt($date->setTime(17, 0));
                $live->setContent($contents[$index]);
                $live->setPlanning($planning);
                $live->setSeason($season);
                $live->setEpisode($episode);
                $manager->persist($live);
                ++$episode;
                ++$index;

                if (14 === $episode) {
                    $episode = 1;
                    ++$season;
                }
            }
        }

        $manager->flush();
    }
}
