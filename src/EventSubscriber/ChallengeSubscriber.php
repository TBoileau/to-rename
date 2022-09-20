<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Challenge;
use App\Entity\ChallengeRule;
use App\Entity\User;
use App\Repository\RuleRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
