<?php

declare(strict_types=1);

namespace App\Command;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebSocket\Client;

#[AsCommand(
    name: 'app:video',
)]
class VideoCommande extends Command
{
    public function __construct(private string $uploadDir)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (file_exists(sprintf('%s/v4.mp4', $this->uploadDir))) {
            unlink(sprintf('%s/v4.mp4', $this->uploadDir));
        }

        $ffmpeg = FFMpeg::create();

        $video = $ffmpeg->open(sprintf('%s/v2.mp4', $this->uploadDir));

        $video
            ->filters()
            ->watermark(sprintf('%s/project.png', $this->uploadDir), [
                'position' => 'absolute',
                'bottom' => 10,
                'right' => 10,
            ]);
        $video->save(new X264(), sprintf('%s/v2-1.mp4', $this->uploadDir));


        $video = $ffmpeg->open(sprintf('%s/v1.mp4', $this->uploadDir));

        $video
            ->concat([
                sprintf('%s/v2-1.mp4', $this->uploadDir),
                sprintf('%s/v3.mp4', $this->uploadDir),
            ])
            ->saveFromSameCodecs(sprintf('%s/v4.mp4', $this->uploadDir), true);


        return Command::SUCCESS;
    }
}
