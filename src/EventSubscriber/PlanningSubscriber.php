<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Live;
use App\Entity\Planning;
use App\Planning\PlanningGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class PlanningSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private PlanningGeneratorInterface $planningGenerator,
        private SluggerInterface $slugger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['beforePlanningPersisted'],
            BeforeEntityUpdatedEvent::class => ['beforePlanningUpdated'],
            AfterEntityDeletedEvent::class => ['afterPlanningDeleted'],
        ];
    }

    public function afterPlanningDeleted(AfterEntityDeletedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Live) {
            $this->generate($entity->getPlanning());
        }
    }

    public function beforePlanningPersisted(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Planning) {
            $this->generate($entity);
        }

        if ($entity instanceof Live) {
            $this->generate($entity->getPlanning());
        }
    }

    public function beforePlanningUpdated(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Planning) {
            $this->generate($entity);
        }

        if ($entity instanceof Live) {
            $this->generate($entity->getPlanning());
        }
    }

    private function generate(Planning $planning): void
    {
        $planning->setImage(sprintf('%s.png', $this->slugger->slug((string) $planning)->toString()));
        $this->planningGenerator->generate($planning);
    }
}
