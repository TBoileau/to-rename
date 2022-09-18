<?php

declare(strict_types=1);

namespace App\Notifier\Twitter;

use App\OAuth\Api\Twitter\TwitterClient;
use App\OAuth\ClientInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Transport\TransportInterface;

final class TwitterTransport implements TransportInterface
{
    /**
     * @param TwitterClient $twitterClient
     */
    public function __construct(private ClientInterface $twitterClient)
    {
    }

    public function send(MessageInterface $message): ?SentMessage
    {
        $tweet = $this->twitterClient->tweet($message->getSubject());

        $sentMessage = new SentMessage($message, (string) $this);
        $sentMessage->setMessageId($tweet['data']['id']);

        return $sentMessage;
    }

    public function supports(MessageInterface $message): bool
    {
        return $message instanceof ChatMessage && 'twitter' === $message->getTransport();
    }

    public function __toString(): string
    {
        return 'twitter://api.twitter.com/2/tweets';
    }
}
