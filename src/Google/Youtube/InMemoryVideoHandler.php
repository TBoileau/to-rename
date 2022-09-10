<?php

declare(strict_types=1);

namespace App\Google\Youtube;

use App\Entity\Video;
use Google_Client;

final class InMemoryVideoHandler extends VideoHandler implements VideoHandlerInterface
{
    public function __construct(
        Google_Client $googleClient,
        private string $googleFixturesSearchList,
        private string $googleFixturesVideosList
    ) {
        parent::__construct($googleClient);
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
