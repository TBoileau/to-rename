<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Live;
use Doctrine\ORM\EntityManagerInterface;
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
            'startedAt' => '2022-01-17 10:30:00',
            'description' => 'PROJET SUR ANGULAR SYMFONY ANGULAR SYMFONY',
        ]]);
        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/api/contexts/Live',
            '@type' => 'Live',
            'startedAt' => '2022-01-17T10:30:00+00:00',
            'description' => 'PROJET SUR ANGULAR SYMFONY ANGULAR SYMFONY',
        ]);
        self::assertMatchesRegularExpression('~^/api/lives/\d+$~', $response->toArray()['@id']);
        self::assertMatchesResourceItemJsonSchema(Live::class);
        self::assertFileExists(sprintf('%s/../../public/uploads/planning_03_2022.png', __DIR__));
    }

    public function testPutLivesWeek(): void
    {
        $response = self::createClient()->request('PUT', '/api/lives/1', ['json' => [
            'startedAt' => '2022-01-03 12:30:00',
            'description' => 'PROJET SUR ANGULAR SYMFONY ANGULAR SYMFONY',
        ]]);
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/api/contexts/Live',
            '@type' => 'Live',
            'startedAt' => '2022-01-03T12:30:00+00:00',
            'description' => 'PROJET SUR ANGULAR SYMFONY ANGULAR SYMFONY',
        ]);
        self::assertMatchesRegularExpression('~^/api/lives/\d+$~', $response->toArray()['@id']);
        self::assertMatchesResourceItemJsonSchema(Live::class);
        self::assertFileExists(sprintf('%s/../../public/uploads/planning_01_2022.png', __DIR__));
    }

    public function testDeleteBook(): void
    {
        $client = static::createClient();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        /** @var Live $live */
        $live = $entityManager->find(Live::class, 5);
        $entityManager->remove($live);
        $entityManager->flush();
        /** @var string $iri */
        $iri = self::findIriBy(Live::class, ['id' => 6]);
        $client->request('DELETE', $iri);
        self::assertResponseStatusCodeSame(204);
        self::assertNull($entityManager->getRepository(Live::class)->find(6));
        self::assertFileDoesNotExist(sprintf('%s/../../public/uploads/planning_02_2022.png', __DIR__));
    }

    public function testPostLivesWeekShouldRaiseAViolationForDescriptionWidth(): void
    {
        self::createClient()->request('POST', '/api/lives', ['json' => [
            'startedAt' => '2022-01-17 10:30:00',
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

    public function testPostLivesWeekShouldRaiseAViolationOfUniqueWeekAndYear(): void
    {
        self::createClient()->request('POST', '/api/lives', ['json' => [
            'startedAt' => '2022-01-10 10:30:00',
            'description' => '123456789',
        ]]);
        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'startedAt: Ce live existe déjà.',
            'violations' => [
                [
                    'propertyPath' => 'startedAt',
                    'message' => 'Ce live existe déjà.',
                    'code' => '23bd9dbf-6b9b-41cd-a99e-4844bcf3077f',
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
