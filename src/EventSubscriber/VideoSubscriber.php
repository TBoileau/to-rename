<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Video;
use App\Google\Youtube\VideoHandlerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class VideoSubscriber implements EventSubscriberInterface
{
    public function __construct(private VideoHandlerInterface $videoHandler)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityUpdatedEvent::class => ['afterVideoUpdated'],
        ];
    }

    public function afterVideoUpdated(AfterEntityUpdatedEvent $event): void
    {
        $video = $event->getEntityInstance();

        if ($video instanceof Video) {
            $this->videoHandler->update($video);
        }
    }
}
