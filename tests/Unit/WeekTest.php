<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Live;
use App\Entity\Week;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class WeekTest extends TestCase
{
    public function testShouldHydrateWeek(): void
    {
        $week = new Week([
            self::createLive('2020-01-03'),
            self::createLive('2020-01-04'),
            self::createLive('2020-01-05'),
            self::createLive('2020-01-06'),
            self::createLive('2020-01-07'),
        ]);

        self::assertEquals(3, $week->startDay);
        self::assertEquals(7, $week->endDay);
        self::assertEquals('janvier', $week->month);
    }

    private static function createLive(string $startedAt): Live
    {
        $live = new Live();
        $live->setStartedAt(new DateTimeImmutable($startedAt));
        $live->setDescription('test');

        return $live;
    }
}
