<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\ContentImage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ContentImageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(self::createContentImage('challenge'));
        $manager->persist(self::createContentImage('getting_started'));
        $manager->persist(self::createContentImage('capsule'));
        $manager->persist(self::createContentImage('code_review'));
        $manager->persist(self::createContentImage('project'));
        $manager->persist(self::createContentImage('podcast'));
        $manager->persist(self::createContentImage('kata'));
        $manager->flush();
    }

    private static function createContentImage(string $name): ContentImage
    {
        $contentImage = new ContentImage();
        $contentImage->setName($name);
        $contentImage->setImage(sprintf('%s.png', $name));

        return $contentImage;
    }
}
