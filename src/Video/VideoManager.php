<?php

declare(strict_types=1);

namespace App\Video;

use App\DataCollector\VideoCollectInterface;
use App\Entity\Video;
use App\OAuth\Api\Google\GoogleClient;
use App\Repository\CategoryRepository;
use App\Repository\VideoRepository;
use App\Video\Youtube\VideoProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Google\Service\YouTube\Video as YoutubeVideo;
use Google_Http_MediaFileUpload;
use Google_Service_YouTube;
use GuzzleHttp\Psr7\Request;

use function Symfony\Component\String\u;

final class VideoManager implements VideoManagerInterface, VideoCollectInterface
{
    private Google_Service_YouTube $youtube;

    /**
     * @var array<array-key, VideoInterface>
     */
    private array $videosUpdated = [];

    public function __construct(
        GoogleClient $googleClient,
        private VideoRepository $videoRepository,
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $entityManager,
        private VideoProviderInterface $videoProvider,
        private string $uploadDir
    ) {
        $this->youtube = new Google_Service_YouTube($googleClient);
    }

    public function synchronize(): void
    {
        $videos = $this->videoProvider->findAll();

        foreach ($videos as $youtubeVideo) {
            $video = $this->videoRepository->findOneBy(['youtubeId' => $youtubeVideo->getId()]);

            if (null === $video) {
                $video = new Video();
                $this->entityManager->persist($video);
            }

            $this->hydrate($video, $youtubeVideo);
        }

        $this->entityManager->flush();
    }

    public function update(VideoInterface $video): void
    {
        $youtubeVideo = $this->videoProvider->findOneById($video->getYoutubeId());

        $videoSnippet = $youtubeVideo->getSnippet();
        $videoSnippet->setDefaultAudioLanguage($video->getDefaultAudioLanguage());
        $videoSnippet->setDefaultLanguage($video->getDefaultLanguage());

        /** @var CategoryInterface $category */
        $category = $video->getCategory();

        $videoSnippet->setTitle(sprintf(
            'S%02dE%02d - %s - %s',
            $video->getSeason(),
            $video->getEpisode(),
            u($video->getTitle())->trim(),
            u($category->getName())->trim(),
        ));
        $videoSnippet->setDescription($video->getDescription());
        $videoSnippet->setTags(array_values($video->getTags()));

        $videoStatus = $youtubeVideo->getStatus();

        $videoStatus->setPrivacyStatus($video->getPrivacyStatus());

        $this->youtube->videos->update(['snippet', 'status'], $youtubeVideo);

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

        $this->videosUpdated[] = $video;
    }

    public function hydrate(VideoInterface $video, ?YoutubeVideo $youtubeVideo = null): void
    {
        if (null === $youtubeVideo) {
            $youtubeVideo = $this->videoProvider->findOneById($video->getYoutubeId());
        }

        $videoSnippet = $youtubeVideo->getSnippet();

        $video->setYoutubeId($youtubeVideo->getId());
        $video->setDescription($videoSnippet->getDescription());
        /* @phpstan-ignore-next-line */
        $video->setTags($videoSnippet->getTags() ?? []);

        if (false !== preg_match('/(S(\d{2})E(\d{2})) - (.+) - (.+)/', $videoSnippet->getTitle(), $matches)) {
            if (isset($matches[1])) {
                /**
                 * @var string $categoryName
                 * @var string $title
                 * @var string $episode
                 * @var string $season
                 */
                [, , $season, $episode, $categoryName, $title] = $matches;

                /** @var CategoryInterface $category */
                $category = $this->categoryRepository->findOneBy(['name' => u($categoryName)->trim()->toString()]);
                $video->setTitle(u($title)->trim()->toString());
                $video->setCategory($category);
                $video->setSeason((int) $season);
                $video->setEpisode((int) $episode);
            }
        } else {
            $video->setTitle($videoSnippet->getTitle());
            $video->setCategory(null);
            $video->setSeason(0);
            $video->setEpisode(0);
        }

        $video->setThumbnail(
            sprintf(
                'S%02dE%02d-%d.png',
                $video->getSeason(),
                $video->getEpisode(),
                $video->getYoutubeId()
            )
        );

        copy(
            $videoSnippet->getThumbnails()->getMaxres()->getUrl(),
            sprintf('%s/%s', $this->uploadDir, $video->getThumbnail())
        );

        $videoStatus = $youtubeVideo->getStatus();

        $video->setPrivacyStatus($videoStatus->getPrivacyStatus());

        $videoStatistics = $youtubeVideo->getStatistics();

        $video->setViews((int) $videoStatistics->getViewCount());
        $video->setLikes((int) $videoStatistics->getLikeCount());
        $video->setComments((int) $videoStatistics->getCommentCount());
    }

    public function updateStatistics(): void
    {
        /** @var array<array-key, Video> $videos */
        $videos = $this->videoRepository->findAll();

        /** @var array<string, Video> $videos */
        $videos = array_combine(
            array_map(
                static fn (Video $video): string => $video->getYoutubeId(),
                $videos
            ),
            $videos
        );

        $page = 1;

        do {
            $videosToUpdate = array_slice($videos, ($page - 1) * 50, 50);

            $youtubeVideos = $this->videoProvider->get(
                array_map(
                    static fn (Video $video): string => $video->getYoutubeId(),
                    $videosToUpdate
                )
            );

            foreach ($youtubeVideos as $youtubeVideo) {
                $video = $videos[$youtubeVideo->getId()];

                $videoStatistics = $youtubeVideo->getStatistics();

                $video->setViews((int) $videoStatistics->getViewCount());
                $video->setLikes((int) $videoStatistics->getLikeCount());
                $video->setComments((int) $videoStatistics->getCommentCount());
            }

            $this->entityManager->flush();

            ++$page;
        } while (ceil(count($videos) / 50) < $page);
    }

    public function getVideosUpdated(): array
    {
        return $this->videosUpdated;
    }
}
