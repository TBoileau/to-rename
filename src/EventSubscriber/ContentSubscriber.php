<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Doctrine\Entity\Live;
use App\Doctrine\Entity\Planning;
use App\UseCase\PlanningGenerator\PlanningGeneratorInterface;
use App\UseCase\ThumbnailGenerator\ThumbnailGeneratorInterface;
use App\Youtube\VideoProviderInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ContentSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private PlanningGeneratorInterface $planningGenerator,
        private ThumbnailGeneratorInterface $thumbnailGenerator,
        private SluggerInterface $slugger,
        private VideoProviderInterface $videoProvider
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postRemove,
        ];
    }

    public function postRemove(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Live) {
            $this->generatePlanning($entity->getPlanning());
        }
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Planning) {
            $this->generatePlanning($entity);
        }

        if ($entity instanceof Live) {
            $this->generatePlanning($entity->getPlanning());
            $this->generateLive($entity);

            if (null !== $entity->getYoutubeId()) {
                $this->videoProvider->hydrate($entity);
                $this->videoProvider->update($entity);
            }
        }
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Planning) {
            $this->generatePlanning($entity);
        }

        if ($entity instanceof Live) {
            $this->generatePlanning($entity->getPlanning());
            $this->generateLive($entity);

            if (null !== $entity->getYoutubeId()) {
                $this->videoProvider->hydrate($entity);

                if ($event->hasChangedField('youtubeId')) {
                    $this->videoProvider->update($entity);
                }
            }

            $this->videoProvider->update($entity);
        }
    }

    private function generatePlanning(Planning $planning): void
    {
        $planning->setImage(sprintf('%s.png', $this->slugger->slug((string) $planning)->toString()));
        $this->planningGenerator->generate($planning);
    }

    private function generateLive(Live $live): void
    {
        $live->setThumbnail(
            sprintf(
                'S%02dE%02d.png',
                $live->getSeason(),
                $live->getEpisode()
            )
        );
        $this->thumbnailGenerator->generate($live);
    }
}
