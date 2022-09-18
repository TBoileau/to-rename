<?php

declare(strict_types=1);

namespace App\SocialNetwork;

use App\OAuth\Security\Token\OAuthToken;
use App\OAuth\Security\Token\TokenStorageInterface;
use Symfony\Component\Notifier\Bridge\Discord\DiscordOptions;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordEmbed;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordMediaEmbedObject;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

final class SocialNetwork implements SocialNetworkInterface
{
    public function __construct(private ChatterInterface $chatter, private TokenStorageInterface $tokenStorage)
    {
    }

    public function send(string $message, ?string $image = null): void
    {
        /** @var OAuthToken $twitterToken */
        $twitterToken = $this->tokenStorage['twitter'];

        if ($twitterToken->isAuthenticated()) {
            $this->chatter->send((new ChatMessage($message))->transport('twitter'));
        }

        /** @var OAuthToken $linkedInToken */
        $linkedInToken = $this->tokenStorage['linkedin'];

        if ($linkedInToken->isAuthenticated()) {
            $this->chatter->send((new ChatMessage($message))->transport('linkedin'));
        }

        $discordMessage = (new ChatMessage($message))->transport('discord');

        if (null !== $image) {
            $discordEmbedObject = (new DiscordMediaEmbedObject())->url($image);
            $discordOptions = (new DiscordOptions())->addEmbed((new DiscordEmbed())->image($discordEmbedObject));
            $discordMessage->options($discordOptions);
        }

        $this->chatter->send($discordMessage);
    }
}
