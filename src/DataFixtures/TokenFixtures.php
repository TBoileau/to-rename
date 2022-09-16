<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Token;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class TokenFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $googleToken = new Token();
        $googleToken->setName('google');
        $manager->persist($googleToken);

        $twitterToken = new Token();
        $twitterToken->setName('twitter');
        $manager->persist($twitterToken);
        $manager->flush();
    }
}
