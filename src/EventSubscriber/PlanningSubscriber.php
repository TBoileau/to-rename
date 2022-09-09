<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Planning;
use App\Generator\PlanningGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
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
        ];
    }

    public function beforePlanningPersisted(BeforeEntityPersistedEvent $event): void
    {
        $planning = $event->getEntityInstance();

        if ($planning instanceof Planning) {
            $planning->setImage(sprintf('%s.png', $this->slugger->slug((string) $planning)->toString()));
            $this->planningGenerator->generate($planning);
        }
    }
}
