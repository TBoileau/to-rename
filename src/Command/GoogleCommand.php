<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Token;
use App\OAuth\ClientInterface;
use App\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:google',
    description: 'Reload refresh token',
)]
final class GoogleCommand extends Command
{
    public function __construct(
        private ClientInterface $googleClient,
        private TokenRepository $tokenRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Token $googleToken */
        $googleToken = $this->tokenRepository->findOneBy(['name' => 'google']);

        if (null === $googleToken->getRefreshToken()) {
            $output->writeln('No refresh token found.');

            return Command::FAILURE;
        }

        /** @var array{access_token?: string, created?: int, expires_in?: int, refresh_token?: string} $accessToken */
        $accessToken = $this->googleClient->fetchAccessTokenWithRefreshToken($googleToken->getRefreshToken());

        if (!isset($accessToken['refresh_token'])) {
            $output->writeln('Error during OAuth2 connection with Google.');

            return Command::FAILURE;
        }

        $googleToken->setRefreshToken($accessToken['refresh_token']);

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
