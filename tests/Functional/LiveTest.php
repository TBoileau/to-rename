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

    public function testPostLivesWeek(): void
    {
        $response = self::createClient()->request('POST', '/api/lives', ['json' => [
            'startedAt' => '2020-01-10 10:30:00',
            'description' => 'PROJET SUR ANGULAR SYMFONY ANGULAR SYMFONY',
        ]]);
        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/api/contexts/Live',
            '@type' => 'Live',
            'startedAt' => '2020-01-10T10:30:00+00:00',
            'description' => 'PROJET SUR ANGULAR SYMFONY ANGULAR SYMFONY',
        ]);
        self::assertMatchesRegularExpression('~^/api/lives/\d+$~', $response->toArray()['@id']);
        self::assertMatchesResourceItemJsonSchema(Live::class);
    }

    public function testPostLivesWeekShouldReturn422(): void
    {
        self::createClient()->request('POST', '/api/lives', ['json' => [
            'startedAt' => '2020-01-10 10:30:00',
            'description' => '1234567890123456',
        ]]);
        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'description: Chaque ligne doit faire 15 caractères maximum',
            'violations' => [
                [
                    'propertyPath' => 'description',
                    'message' => 'Chaque ligne doit faire 15 caractères maximum',
                    'code' => null,
                ],
            ],
        ]);
    }

    public function provideWeeks(): Generator
    {
        yield 'week 1 of 2022' => [1, 2022, 4];
        yield 'week 2 of 2022' => [2, 2022, 2];
    }
}
