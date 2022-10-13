<?php

declare(strict_types=1);

namespace App\UseCase\BotCommand;

interface BotCommandInterface
{
    public function __invoke(string $nickname, string $command): ?string;
}
