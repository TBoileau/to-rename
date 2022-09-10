<?php

declare(strict_types=1);

namespace App\Google\Youtube;

use App\Entity\Video;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Google\Service\YouTube\Thumbnail;
use Google\Service\YouTube\Video as YoutubeVideo;
use Google_Client;
use Google_Http_MediaFileUpload;
use Google_Service_YouTube;
use GuzzleHttp\Psr7\Request;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

use function Symfony\Component\String\u;

class VideoHandler implements VideoHandlerInterface, VideoSynchronizerInterface
{
    protected Google_Service_YouTube $youtube;

    public function __construct(
        Google_Client $googleClient,
        private string $uploadDir,
        private EntityManagerInterface $entityManager,
        private VideoRepository $videoRepository
    ) {
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

        $this->youtube->getClient()->setDefer(true);

        /** @var Request $thumbnailSetRequest */
        $thumbnailSetRequest = $this->youtube->thumbnails->set($video->getYoutubeId());

        $chunkSizeBytes = 1 * 1024 * 1024;

        $imagePath = sprintf('%s/%s', $this->uploadDir, $video->getThumbnail());

        $media = new Google_Http_MediaFileUpload(
            $this->youtube->getClient(),
            $thumbnailSetRequest,
            'image/png',
            '',
            true,
            $chunkSizeBytes
        );

        /** @var int $fileSize */
        $fileSize = filesize($imagePath);

        $media->setFileSize($fileSize);

        $status = false;

        /** @var resource $handle */
        $handle = fopen($imagePath, 'rb');

        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }

        fclose($handle);

        $this->youtube->getClient()->setDefer(false);

        $this->syncOne($video);
    }

    public function syncOne(Video $video): void
    {
        $videos = $this->get([$video->getYoutubeId()]);

        $this->handleVideo($videos[0]);

        $this->entityManager->flush();
    }

    public function syncAll(): void
    {
        $videos = $this->list();

        foreach ($videos as $youtubeVideo) {
            $this->handleVideo($youtubeVideo);
        }
    }

    private function handleVideo(YoutubeVideo $youtubeVideo): void
    {
        $video = $this->videoRepository->findOneBy(['youtubeId' => $youtubeVideo->getId()]);

        if (null === $video) {
            $video = new Video();
            $video->setYoutubeId($youtubeVideo->getId());
            $this->entityManager->persist($video);
        }

        $video->setTitle($youtubeVideo->getSnippet()->getTitle());

        if (preg_match('/(S(\d{2})E(\d{2}))/', $video->getTitle(), $matches) !== false) {
            [, $info, $season, $episode] = $matches;
            $video->setTitle(u($video->getTitle())->replace($info, '')->trim()->trimStart('-')->toString());
            $video->setSeason((int) $season);
            $video->setEpisode((int) $episode);
        } else {
            $video->setSeason(0);
            $video->setEpisode(0);
        }

        $video->setDescription($youtubeVideo->getSnippet()->getDescription());
        $video->setTags($youtubeVideo->getSnippet()->getTags());

        /** @var array<string, string> $thumbnails */
        $thumbnails = [];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach (['default', 'medium', 'high', 'standard', 'maxres'] as $type) {
            /** @var Thumbnail $thumbnail */
            $thumbnail = $propertyAccessor->getValue($youtubeVideo->getSnippet()->getThumbnails(), $type);
            $thumbnails[$type] = $thumbnail->getUrl();
        }

        $video->setThumbnails($thumbnails);
    }
}
