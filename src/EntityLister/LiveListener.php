<?php

declare(strict_types=1);

namespace App\EntityLister;

use App\Entity\Live;
use App\Generator\PlanningGeneratorInterface;
use App\Repository\LiveRepository;
use Symfony\Component\Filesystem\Filesystem;

final class LiveListener
{
    public function __construct(
        private PlanningGeneratorInterface $planningGenerator,
        private LiveRepository $liveRepository,
        private string $uploadDir
    ) {
    }

    public function generatePlanning(Live $live): void
    {
        $this->planningGenerator->generate(
            sprintf(
                'planning_%s_%s.png',
                $live->getStartedAt()->format('W'),
                $live->getStartedAt()->format('Y'),
            ),
            $this->liveRepository->getWeekByLive($live)
        );
    }

    public function onRemove(Live $live): void
    {
        $lives = $this->liveRepository->getWeekLivesByLive($live);

        if (1 === count($lives)) {
            $filesystem = new Filesystem();
            $filename = sprintf(
                '%s/planning_%s_%s.png',
                $this->uploadDir,
                $lives[0]->getStartedAt()->format('W'),
                $lives[0]->getStartedAt()->format('Y')
            );
            if ($filesystem->exists($filename)) {
                $filesystem->remove($filename);
            }
        }
    }
}
