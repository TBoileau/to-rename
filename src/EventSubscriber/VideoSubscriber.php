<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Video;
use App\Generator\ThumbnailGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class VideoSubscriber implements EventSubscriberInterface
{
    public function __construct(private ThumbnailGeneratorInterface $thumbnailGenerator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['beforeVideoPersisted'],
        ];
    }

    public function beforeVideoPersisted(BeforeEntityPersistedEvent $event): void
    {
        $video = $event->getEntityInstance();

        if ($video instanceof Video) {
            $video->setThumbnail(sprintf('S%02dE%02d.png', $video->getSeason(), $video->getEpisode()));
            $this->thumbnailGenerator->generate($video);
        }
    }
}
