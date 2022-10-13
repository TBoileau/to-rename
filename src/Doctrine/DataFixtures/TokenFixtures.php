<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Token;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class TokenFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $googleToken = new Token();
        $googleToken->setName('google');
        $manager->persist($googleToken);

        $twitchToken = new Token();
        $twitchToken->setName('twitch');
        $manager->persist($twitchToken);

        $manager->flush();
    }
}
