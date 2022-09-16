<?php

declare(strict_types=1);

namespace App\DataCollector;

use Google\Service\YouTube\Video;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class YoutubeCollector extends AbstractDataCollector
{
    public function __construct(private VideoCollectInterface $videoCollect)
    {
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $this->data = [
            'videosUpdated' => $this->videoCollect->getVideosUpdated(),
        ];
    }

    public static function getTemplate(): ?string
    {
        return 'data_collector/youtube.html.twig';
    }

    /**
     * @return array<array-key, Video>
     */
    public function getVideosUpdated(): array
    {
        return $this->data['videosUpdated'];
    }
}
