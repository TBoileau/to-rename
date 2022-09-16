<?php

declare(strict_types=1);

namespace App\Video\Youtube;

use App\OAuth\Api\Google\GoogleClient;
use Google\Service\YouTube\Video;
use Google_Service_YouTube;

final class VideoProvider implements VideoProviderInterface
{
    private Google_Service_YouTube $youtube;

    public function __construct(GoogleClient $googleClient)
    {
        $this->youtube = new Google_Service_YouTube($googleClient);
    }

    public function findOneById(string $id): Video
    {
        $videos = $this->get([$id]);

        if (0 === count($videos)) {
            throw new VideoNotFoundException(sprintf('Video %s not found.', $id));
        }

        return $videos[0];
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(): iterable
    {
        $pageToken = [];

        do {
            $response = $this->youtube->search->listSearch(
                'snippet',
                [
                    'channelId' => 'UCpMntmV07jvZMK3fqR196QQ',
                    'maxResults' => 50,
                    'order' => 'date',
                    'type' => 'video',
                ] + ($pageToken ? ['pageToken' => $pageToken] : [])
            );

            $ids = [];

            foreach ($response->getItems() as $item) {
                $ids[] = $item->getId()->getVideoId();
            }

            yield from $this->get($ids);
        } while (($pageToken = $response->nextPageToken) !== null);
    }

    /**
     * @param array<array-key, string> $ids
     *
     * @return array<array-key, Video>
     */
    private function get(array $ids): array
    {
        $response = $this->youtube->videos->listVideos(['snippet', 'status'], [
            'id' => $ids,
        ]);

        return $response->getItems();
    }
}
