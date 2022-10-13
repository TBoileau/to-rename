<?php

declare(strict_types=1);

namespace App\Command;

use App\OAuth\ClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:oauth',
    description: 'Reload refresh tokens',
)]
final class GoogleCommand extends Command
{
    /**
     * @param iterable<string, ClientInterface> $clients
     */
    public function __construct(private iterable $clients)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->clients as $client) {
            $client->refresh();
        }

        return Command::SUCCESS;
    }
}
