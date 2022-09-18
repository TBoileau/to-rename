<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Video;
use App\OAuth\Security\Token\TokenInterface;
use App\OAuth\Security\Token\TokenStorageInterface;
use App\Video\Thumbnail\ThumbnailGeneratorInterface;
use App\Video\VideoManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class VideoSubscriber implements EventSubscriberInterface
{
    private TokenInterface $googleToken;

    public function __construct(
        private ThumbnailGeneratorInterface $thumbnailGenerator,
        private VideoManagerInterface $videoManager,
        TokenStorageInterface $tokenStorage
    ) {
        /** @phpstan-ignore-next-line */
        $this->googleToken = $tokenStorage['google'];
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

            if ($this->googleToken->isAuthenticated()) {
                $this->videoManager->update($video);
            }
        }
    }

    public function beforeVideoPersisted(BeforeEntityPersistedEvent $event): void
    {
        $video = $event->getEntityInstance();

        if ($video instanceof Video && $this->googleToken->isAuthenticated()) {
            $this->videoManager->hydrate($video);
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
