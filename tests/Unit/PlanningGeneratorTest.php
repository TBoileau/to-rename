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
            self::createLive('2022-01-03 05:00:00'),
            self::createLive('2022-01-04 12:30:00'),
            self::createLive('2022-01-05 12:30:00'),
            self::createLive('2022-01-06 01:00:00'),
            self::createLive('2022-01-07 11:11:11'),
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

    private static function createLive(string $startedAt): Live
    {
        $live = new Live();
        $live->setStartedAt(new DateTimeImmutable($startedAt));
        $live->setDescription(<<<EOL
PROJET123456789
SUR ANGULAR
SYMFONY
EOL
        );

        return $live;
    }
}
