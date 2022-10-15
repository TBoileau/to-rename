<?php

declare(strict_types=1);

namespace App\Command;

use App\UseCase\ScheduleNewsletter\ScheduleNewsletterInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:newsletter:schedule',
    description: 'Schedule to newsletter',
)]
final class ScheduleNewsletterCommand extends Command
{
    public function __construct(private readonly ScheduleNewsletterInterface $scheduleNewsletter)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->scheduleNewsletter->schedule();
        } catch (Exception $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
