<?php

declare(strict_types=1);

namespace App\Notifier\Twitter;

use App\OAuth\Api\Twitter\TwitterClient;
use Symfony\Component\Notifier\Transport\Dsn;
use Symfony\Component\Notifier\Transport\TransportFactoryInterface;
use Symfony\Component\Notifier\Transport\TransportInterface;

final class TwitterTransportFactory implements TransportFactoryInterface
{
    public function __construct(private TwitterClient $twitterClient)
    {
    }

    public function supports(Dsn $dsn): bool
    {
        return 'twitter' === $dsn->getScheme();
    }

    public function create(Dsn $dsn): TransportInterface
    {
        return new TwitterTransport($this->twitterClient);
    }
}
