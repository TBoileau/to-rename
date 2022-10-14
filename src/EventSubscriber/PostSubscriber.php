<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Doctrine\Entity\Post;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

final class PostSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.post.completed.publish' => 'onCompletePublish',
        ];
    }

    public function onCompletePublish(Event $event): void
    {
        /** @var Post $post */
        $post = $event->getSubject();
        $post->setPublishedAt(new DateTimeImmutable());
    }
}
