<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Doctrine\Entity\Newsletter;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SendinBlue\Client\Api\EmailCampaignsApi;

final class NewsletterSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EmailCampaignsApi $emailCampaignsApi)
    {
    }

    public function getSubscribedEvents(): array
    {
        return ['postLoad'];
    }

    public function postLoad(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if (!$entity instanceof Newsletter || null === $entity->getCampaignId()) {
            return;
        }

        /** @var array{globalStats: object} $result */
        $result = $this->emailCampaignsApi->getEmailCampaign($entity->getCampaignId())->getStatistics();

        /**
         * @var array{
         *      uniqueClicks: int,
         *      clickers: int,
         *      complaints: int,
         *      delivered: int,
         *      sent: int,
         *      softBounces: int,
         *      hardBounces: int,
         *      uniqueViews: int,
         *      viewed: int,
         *      unsubscriptions: int,
         *      trackableViews: int,
         * } $statistics
         */
        $statistics = (array) $result['globalStats'];

        $entity->setUniqueClick($statistics['uniqueClicks']);
        $entity->setClickers($statistics['clickers']);
        $entity->setComplaints($statistics['complaints']);
        $entity->setDelivered($statistics['delivered']);
        $entity->setSent($statistics['sent']);
        $entity->setSoftBounces($statistics['softBounces']);
        $entity->setHardBounces($statistics['hardBounces']);
        $entity->setUniqueViews($statistics['uniqueViews']);
        $entity->setViewed($statistics['viewed']);
        $entity->setUnsubscriptions($statistics['unsubscriptions']);
        $entity->setTrackableViews($statistics['trackableViews']);
    }
}
