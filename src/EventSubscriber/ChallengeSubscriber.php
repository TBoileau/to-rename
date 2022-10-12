<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Doctrine\Entity\Challenge;
use App\Doctrine\Entity\ChallengeRule;
use App\Doctrine\Repository\RuleRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ChallengeSubscriber implements EventSubscriberInterface
{
    public function __construct(private RuleRepository $ruleRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['beforeChallengePersisted'],
        ];
    }

    public function beforeChallengePersisted(BeforeEntityPersistedEvent $event): void
    {
        $challenge = $event->getEntityInstance();

        if ($challenge instanceof Challenge) {
            $randomRules = $this->ruleRepository->get10RandomRules();

            foreach ($randomRules as $rule) {
                $challenge->addRule(ChallengeRule::createFromRule($rule));
            }
        }
    }
}
