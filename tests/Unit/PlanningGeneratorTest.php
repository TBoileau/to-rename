<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Live;
use App\Entity\Week;
use App\Generator\PlanningGenerator;
use DateTimeImmutable;
use Intervention\Image\ImageManager;
use PHPUnit\Framework\TestCase;

final class PlanningGeneratorTest extends TestCase
{
    public function testShouldHydrateWeek(): void
    {
        $week = new Week([
            self::createLive('2022-09-05 20:00:00', <<<EOL
PROJET
IL ETAIT UNE FOIS UN DEV
EOL
        ),
            self::createLive('2022-09-06 17:00:00', <<<EOL
GETTING STARTED
TWIG COMPONENT
EOL
            ),
            self::createLive('2022-09-07 17:00:00', <<<EOL
CAPSULE PHP
PROXY PATTERN
EOL
            ),
            self::createLive('2022-09-08 19:00:00', <<<EOL
PROJET
IL ETAIT UNE FOIS UN DEV
EOL
            ),
            self::createLive('2022-09-09 20:00:00', <<<EOL
JUST CHATTING
SUJET LIBRE
EOL
            ),
        ]);

        $generator = new PlanningGenerator(
            new ImageManager(['driver' => 'imagick']),
            __DIR__.'/../../public/images/planning.png',
            __DIR__.'/../../public/fonts/Thunder-BoldLC.ttf',
            __DIR__.'/../../public/fonts/MonumentExtended-Regular.otf',
            __DIR__.'/../../public/uploads',
        );

        $filename = __DIR__.'/../../public/uploads/planning.png';

        if (file_exists($filename)) {
            unlink($filename);
        }

        $generator->generate('planning.png', $week);

        self::assertFileExists($filename);
    }

    private static function createLive(string $startedAt, string $description): Live
    {
        $live = new Live();
        $live->setStartedAt(new DateTimeImmutable($startedAt));
        $live->setDescription($description);

        return $live;
    }
}
