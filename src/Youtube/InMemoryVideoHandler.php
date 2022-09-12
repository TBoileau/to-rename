<?php

declare(strict_types=1);

namespace App\Youtube;

use App\Entity\Video;
use App\OAuth\Api\Google\GoogleClient;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;

final class InMemoryVideoHandler extends VideoHandler implements VideoHandlerInterface
{
    public function __construct(
        GoogleClient $googleClient,
        EntityManagerInterface $entityManager,
        VideoRepository $videoRepository,
        private string $googleFixturesSearchList,
        private string $googleFixturesVideosList,
        string $uploadDir
    ) {
        parent::__construct($googleClient, $uploadDir, $entityManager, $videoRepository);
    }

    public function get(array $ids): array
    {
        $videoList = require $this->googleFixturesVideosList;

        /** @var array<array-key, Video> $videos */
        $videos = [];

        foreach ($ids as $id) {
            $videos[] = $videoList($id);
        }

        return $videos;
    }

    public function list(): iterable
    {
        $pageToken = null;

        $data = [];

        do {
            $searchListResponse = require $this->googleFixturesSearchList;

            $response = $searchListResponse($pageToken);

            $ids = [];

            foreach ($response->getItems() as $item) {
                $ids[] = $item->getId()->getVideoId();
            }

            yield from $this->get($ids);
        } while (($pageToken = $response->nextPageToken) !== null);
    }

    public function update(Video $video): void
    {
    }
}
