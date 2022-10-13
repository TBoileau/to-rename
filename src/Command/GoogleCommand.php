<?php

declare(strict_types=1);

namespace App\Command;

use App\Doctrine\Entity\Token;
use App\Doctrine\Repository\TokenRepository;
use App\OAuth\ClientInterface;
use Doctrine\ORM\EntityManagerInterface;
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
    public function __construct(
        private iterable $clients,
        private TokenRepository $tokenRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->clients as $client) {
            /** @var Token $token */
            $token = $this->tokenRepository->findOneBy(['name' => $client::getName()]);

            if (null === $token->getRefreshToken()) {
                $output->writeln(sprintf('No refresh token found for %s.', $client::getName()));

                continue;
            }

            /** @var array{access_token?: string, created?: int, expires_in?: int, refresh_token?: string} $accessToken */
            $accessToken = $client->fetchAccessTokenWithRefreshToken($token->getRefreshToken());

            if (!isset($accessToken['refresh_token'])) {
                $output->writeln('Error during OAuth2 connection with Google.');

                return Command::FAILURE;
            }

            $token->setRefreshToken($accessToken['refresh_token']);

            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
