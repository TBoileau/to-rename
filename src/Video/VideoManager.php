<?php

declare(strict_types=1);

namespace App\Video;

use App\Entity\Live;
use App\Entity\Video;
use App\Video\Youtube\VideoProviderInterface;

final class VideoManager implements VideoManagerInterface
{
    public function __construct(private VideoProviderInterface $videoProvider)
    {
    }

    public function hydrate(Live $live): void
    {
        $youtubeVideo = $this->videoProvider->findOneById($live->getYoutubeId());

        $videoSnippet = $youtubeVideo->getSnippet();

        if (null === $live->getVideo()) {
            $video = new Video();
            $live->setVideo($video);
        }
        $video = $live->getVideo();

        /** @phpstan-ignore-next-line */
        $thumbnail = null === $videoSnippet->getThumbnails()->getMaxres()
            ? $videoSnippet->getThumbnails()->getHigh()
            : $videoSnippet->getThumbnails()->getStandard();

        $video->setThumbnail($thumbnail->getUrl());

        $videoStatus = $youtubeVideo->getStatus();

        $video->setPrivacyStatus($videoStatus->getPrivacyStatus());

        $videoStatistics = $youtubeVideo->getStatistics();

        $video->setViews((int) $videoStatistics->getViewCount());
        $video->setLikes((int) $videoStatistics->getLikeCount());
        $video->setComments((int) $videoStatistics->getCommentCount());
    }
}
