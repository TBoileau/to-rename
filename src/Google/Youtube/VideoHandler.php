<?php

declare(strict_types=1);

namespace App\Google\Youtube;

use App\Entity\Video;
use Google\Service\YouTube\Video as YoutubeVideo;
use Google_Client;
use Google_Service_YouTube;
use RuntimeException;

class VideoHandler implements VideoHandlerInterface
{
    protected Google_Service_YouTube $youtube;

    public function __construct(Google_Client $googleClient)
    {
        if ($googleClient->isAccessTokenExpired()) {
            throw new RuntimeException('Google access token is expired');
        }

        $this->youtube = new Google_Service_YouTube($googleClient);
    }

    public function get(array $ids): array
    {
        $response = $this->youtube->videos->listVideos('snippet', [
            'id' => $ids,
        ]);

        return $response->getItems();
    }

    public function list(): iterable
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

    public function update(Video $video): void
    {
        $listResponse = $this->get([$video->getYoutubeId()]);

        /** @var YoutubeVideo $videoYoutube */
        $videoYoutube = $listResponse[0];

        $videoSnippet = $videoYoutube->getSnippet();
        $videoSnippet->setDefaultAudioLanguage('FR');
        $videoSnippet->setDefaultLanguage('FR');
        $videoSnippet->setTitle($video->getTitle());
        $videoSnippet->setDescription($video->getDescription());
        $videoSnippet->setTags(array_values($video->getTags()));

        $this->youtube->videos->update('snippet', $videoYoutube);
    }
}
