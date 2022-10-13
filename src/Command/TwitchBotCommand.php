<?php

declare(strict_types=1);

namespace App\Command;

use App\OAuth\ClientInterface;
use App\UseCase\BotCommand\BotCommandInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebSocket\Client;

#[AsCommand(
    name: 'app:twitch:bot',
    description: 'Twitch Bot',
)]
final class TwitchBotCommand extends Command
{
    public function __construct(
        private readonly ClientInterface $twitchClient,
        private readonly BotCommandInterface $botCommand
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new Client('ws://irc-ws.chat.twitch.tv:80', ['filter' => ['text']]);

        $client->setTimeout(-1);

        $this->twitchClient->refresh();

        if ($this->twitchClient->isAccessTokenExpired()) {
            $output->writeln('<error>You need to connect to Twitch with OAuth Grant Flow.</error>');

            return Command::FAILURE;
        }

        /** @var array{created: int, access_token: string, expires_in: int, refresh_token: string} $accessToken */
        $accessToken = $this->twitchClient->getAccessToken();

        $client->text(sprintf('PASS oauth:%s', $accessToken['access_token']));
        $client->text('NICK bot_barnabe');
        $client->text('JOIN #toham');

        while (true) {
            try {
                $message = $client->receive();
                $output->writeln($message);

                if (1 === preg_match('/^PING (.*)$/', $message, $matches)) {
                    [, $message] = $matches;

                    $client->text(sprintf('PONG %s', $message));
                }

                if (1 === preg_match('/^:(.+)!.+ PRIVMSG #toham :.*(![a-z-]+)/', $message, $matches)) {
                    [, $nickname, $command] = $matches;

                    if (($message = $this->botCommand->__invoke($nickname, $command)) !== null) {
                        $client->text(sprintf('PRIVMSG #toham :%s', $message));
                    }
                }
            } catch (Exception $e) {
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
                break;
            }
        }

        $client->close();

        return Command::SUCCESS;
    }
}
