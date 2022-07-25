<?php

declare(strict_types=1);

namespace App\Generator;

use App\Entity\Live;
use App\Entity\Week;
use Intervention\Image\AbstractFont;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Imagick\Font;

use function Symfony\Component\String\u;

final class PlanningGenerator implements PlanningGeneratorInterface
{
    public function __construct(
        private ImageManager $imageManager,
        private string $planningImage,
        private string $fontTitle,
        private string $fontTime,
        private string $uploadDir
    ) {
    }

    public function generate(string $filename, Week $week): void
    {
        $image = $this->imageManager
            ->make($this->planningImage)
            ->text($week->__toString(), 1835, 160, fn (AbstractFont $font) => $font
                ->file($this->fontTitle)
                ->size(64)
                ->color('#FFFFFF')
                ->align('right')
                ->valign('top')
            );

        $xTitle = 64;
        $xTime = 64;

        foreach ($week->getLives() as $live) {
            if (null !== $live) {
                $this->createLiveDay($live, $image, $xTitle, $xTime);
            } else {
                $this->createNoLiveDay($image, $xTitle, $xTime);
            }
            $xTitle += 366;
            $xTime += 364;
        }

        $image->save(sprintf('%s/%s', $this->uploadDir, $filename));
    }

    private function createNoLiveDay(Image $image, int $xTitle, int $xTime): void
    {
        $this->createLiveDescription('OFFLINE', $image, $xTitle, '#b3b3b3');
        $this->fillTime($image, $xTime);
    }

    private function createLiveDay(Live $live, Image $image, int $xTitle, int $xTime): void
    {
        $this->createLiveDescription($live->getDescription(), $image, $xTitle);
        $this->createLiveTime($live->getStartedAt()->format('H\Hi'), $image, $xTime);
    }

    private function fillTime(Image $image, int $x): void
    {
        $x = ($x + 95);
        $y = 751;
        $image->rectangle(
            $x,
            $y,
            $x + 160,
            $y + 42,
            function ($draw) {
                $draw->background('#ffffff');
            }
        );
    }

    private function createLiveTime(string $time, Image $image, int $x): void
    {
        $font = (new Font($time))
            ->file($this->fontTime)
            ->size(26)
            ->color('#FFFFFF')
            ->align('center')
            ->valign('center');

        ['width' => $width, 'height' => $height] = $font->getBoxSize();

        $font->applyToImage(
            $image,
            ($x + 99) + (136 - $width) / 2 + ($width / 2),
            766 + (39 - $height) / 2,
        );
    }

    private function createLiveDescription(string $description, Image $image, int $x, string $color = '#00153f'): void
    {
        $font = (new Font(u($description)->wordwrap(15, "\n")->upper()->toString()))
            ->file($this->fontTitle)
            ->size(52)
            ->color($color)
            ->align('center')
            ->valign('center');

        ['width' => $width, 'height' => $height] = $font->getBoxSize();

        $font->applyToImage(
            $image,
            $x + (330 - $width) / 2 + ($width / 2),
            442 + (370 - $height) / 2,
        );
    }
}
