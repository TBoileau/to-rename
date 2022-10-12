<?php

declare(strict_types=1);

namespace App\Youtube;

use App\OAuth\GoogleClient;
use Google\Service\YouTube\Video;
use Google_Http_MediaFileUpload;
use Google_Service_YouTube;
use GuzzleHttp\Psr7\Request;
use Twig\Environment;

final class VideoProvider implements VideoProviderInterface
{
    private Google_Service_YouTube $youtube;

    public function __construct(GoogleClient $googleClient, private string $uploadDir, private Environment $twig)
    {
        $this->youtube = new Google_Service_YouTube($googleClient);
    }

    public function find(string $id): Video
    {
        $response = $this->youtube->videos->listVideos(['snippet', 'status', 'statistics'], [
            'id' => [$id],
        ]);

        $videos = $response->getItems();

        if (0 === count($videos)) {
            throw new VideoNotFoundException(sprintf('Video %s not found.', $id));
        }

        return $videos[0];
    }

    public function hydrate(YoutubeVideoInterface $video): void
    {
        if (null === $video->getYoutubeId()) {
            throw new VideoNotFoundException('Video not found.');
        }

        $youtubeVideo = $this->find($video->getYoutubeId());
        $videoStatus = $youtubeVideo->getStatus();
        $videoStatistics = $youtubeVideo->getStatistics();

        /** @var string $status */
        $status = $videoStatus->getPrivacyStatus();

        $views = (int) $videoStatistics->getViewCount();

        $likes = (int) $videoStatistics->getLikeCount();

        $comments = (int) $videoStatistics->getCommentCount();

        $video->setVideo($status, $views, $likes, $comments);
    }

    public function update(YoutubeVideoInterface $video): void
    {
        if (null === $video->getYoutubeId()) {
            throw new VideoNotFoundException('Video not found.');
        }

        $youtubeVideo = $this->find($video->getYoutubeId());

        $videoSnippet = $youtubeVideo->getSnippet();
        $videoSnippet->setDefaultAudioLanguage('FR');
        $videoSnippet->setDefaultLanguage('FR');
        $videoSnippet->setTitle($video->getVideoTitle());
        $videoSnippet->setTags($video->getVideoTags());

        $videoSnippet->setDescription(
            $this->twig
                ->createTemplate($video->getVideoDescription())
                ->render(['live' => $video])
        );

        $videoStatus = $youtubeVideo->getStatus();

        $videoStatus->setPrivacyStatus($video->getVideoStatus());

        $this->youtube->videos->update(['snippet', 'status', 'statistics'], $youtubeVideo);

        $this->youtube->getClient()->setDefer(true);

        /** @var Request $thumbnailSetRequest */
        $thumbnailSetRequest = $this->youtube->thumbnails->set($video->getYoutubeId());

        $chunkSizeBytes = 1 * 1024 * 1024;

        $imagePath = sprintf('%s/%s', $this->uploadDir, $video->getVideoThumbnail());

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
    }
}
