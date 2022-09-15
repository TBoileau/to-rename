<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Video;
use App\Generator\ThumbnailGeneratorInterface;
use App\Youtube\VideoHandlerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class VideoSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ThumbnailGeneratorInterface $thumbnailGenerator,
        private VideoHandlerInterface $videoHandler
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['beforeVideoPersisted'],
            BeforeEntityUpdatedEvent::class => ['beforeVideoUpdated'],
        ];
    }

    public function beforeVideoUpdated(BeforeEntityUpdatedEvent $event): void
    {
        $video = $event->getEntityInstance();

        if ($video instanceof Video) {
            $this->generate($video);
            $this->videoHandler->update($video);
        }
    }

    public function beforeVideoPersisted(BeforeEntityPersistedEvent $event): void
    {
        $video = $event->getEntityInstance();

        if ($video instanceof Video) {
            $this->videoHandler->hydrateVideo($video);
        }
    }

    private function generate(Video $video): void
    {
        $video->setThumbnail(
            sprintf(
                'S%02dE%02d-%d.png',
                $video->getSeason(),
                $video->getEpisode(),
                $video->getId()
            )
        );
        $this->thumbnailGenerator->generate($video);
    }
}
