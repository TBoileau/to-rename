<?php

declare(strict_types=1);

namespace App\Youtube;

use App\Entity\Video;
use App\OAuth\Api\Google\GoogleClient;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Google\Service\YouTube\Video as YoutubeVideo;

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
        $listResponse = $this->get([$video->getYoutubeId()]);

        /** @var YoutubeVideo $videoYoutube */
        $videoYoutube = $listResponse[0];

        $videoSnippet = $videoYoutube->getSnippet();
        $videoSnippet->setDefaultAudioLanguage('FR');
        $videoSnippet->setDefaultLanguage('FR');
        $videoSnippet->setTitle(
            sprintf(
                'S%02dE%02d - %s',
                $video->getSeason(),
                $video->getEpisode(),
                $video->getTitle()
            )
        );
        $videoSnippet->setDescription($video->getDescription());
        $videoSnippet->setTags(array_values($video->getTags()));

        $this->videosUpdated[] = $videoYoutube;
    }
}
