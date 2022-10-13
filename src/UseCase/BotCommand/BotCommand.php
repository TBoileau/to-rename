<?php

declare(strict_types=1);

namespace App\UseCase\BotCommand;

use App\Doctrine\Entity\Command;
use App\Doctrine\Entity\Live;
use App\Doctrine\Repository\CommandRepository;
use App\Doctrine\Repository\LiveRepository;
use DateTimeImmutable;
use Twig\Environment;

final class BotCommand implements BotCommandInterface
{
    public function __construct(
        private CommandRepository $commandRepository,
        private LiveRepository $liveRepository,
        private Environment $twig
    ) {
    }

    public function __invoke(string $nickname, string $command): ?string
    {
        $sendMessage = fn (Command $command, ?Live $live = null): string => $this->twig
            ->createTemplate($command->getTemplate())
            ->render(['nickname' => $nickname, 'live' => $live]);

        $criteria = ['name' => $command, 'category' => null];

        /** @var ?Command $command */
        $command = $this->commandRepository->findOneBy($criteria);

        if (null !== $command) {
            return $sendMessage($command);
        }

        $live = $this->liveRepository->getLiveByDate(new DateTimeImmutable());

        if (null === $live) {
            return null;
        }

        $criteria['category'] = $live->getContent()->getCategory();

        /** @var ?Command $command */
        $command = $this->commandRepository->findOneBy($criteria);

        if (null === $command) {
            return null;
        }

        return $sendMessage($command, $live);
    }
}
