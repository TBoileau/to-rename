<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Video;
use App\Generator\ThumbnailGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Google\Service\YouTube\Video as YoutubeVideo;
use Google_Client;
use Google_Service_YouTube;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class VideoSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ThumbnailGeneratorInterface $thumbnailGenerator,
        private Google_Client $googleClient
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['beforeVideoPersisted'],
            BeforeEntityUpdatedEvent::class => ['beforeVideoUpdated'],
            AfterEntityPersistedEvent::class => ['afterVideoPersisted'],
            AfterEntityUpdatedEvent::class => ['afterVideoUpdated'],
        ];
    }

    public function beforeVideoUpdated(BeforeEntityUpdatedEvent $event): void
    {
        $video = $event->getEntityInstance();

        if ($video instanceof Video) {
            $this->generate($video);
        }
    }

    public function beforeVideoPersisted(BeforeEntityPersistedEvent $event): void
    {
        $video = $event->getEntityInstance();

        if ($video instanceof Video) {
            $this->generate($video);
        }
    }

    public function afterVideoPersisted(AfterEntityPersistedEvent $event): void
    {
        $video = $event->getEntityInstance();

        if ($video instanceof Video) {
            $this->updateYoutubeVideo($video);
        }
    }

    public function afterVideoUpdated(AfterEntityUpdatedEvent $event): void
    {
    }

    private function updateYoutubeVideo(Video $video): void
    {
        $youtube = new Google_Service_YouTube($this->googleClient);

        preg_match('/v=(.*)/', $video->getLink(), $matches);

        [, $videoId] = $matches;

        $listResponse = $youtube->videos->listVideos('snippet', ['id' => $videoId]);

        /** @var YoutubeVideo $videoYoutube */
        $videoYoutube = $listResponse[0];

        $videoSnippet = $videoYoutube->getSnippet();

        $videoSnippet->setTitle($video->getTitle());

        $youtube->videos->update('snippet', $videoYoutube);
    }

    private function generate(Video $video): void
    {
        $video->setThumbnail(sprintf('S%02dE%02d.png', $video->getSeason(), $video->getEpisode()));
        $this->thumbnailGenerator->generate($video);
    }
}
