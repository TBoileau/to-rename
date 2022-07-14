<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Live;
use Generator;

final class LiveTest extends ApiTestCase
{
    /**
     * @dataProvider provideWeeks
     */
    public function testGetLivesByWeek(int $week, int $year, int $numberOfLives): void
    {
        $response = self::createClient()->request('GET', sprintf('/api/lives?week=%d&year=%d', $week, $year));
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertCount($numberOfLives, $response->toArray()['hydra:member']);
        self::assertMatchesResourceCollectionJsonSchema(Live::class);
    }

    public function provideWeeks(): Generator
    {
        yield 'week 1 of 2022' => [1, 2022, 4];
        yield 'week 2 of 2022' => [2, 2022, 2];
    }
}
