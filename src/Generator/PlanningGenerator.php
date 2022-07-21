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
        private string $fontTime
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

        foreach ($week->lives as $live) {
            $this->createLiveDescription($live, $image, $xTitle);
            $this->createLiveTime($live, $image, $xTime);
            $xTitle += 366;
            $xTime += 364;
        }

        $image->save($filename);
    }

    private function createLiveTime(Live $live, Image $image, int $x): void
    {
        $font = (new Font($live->getStartedAt()->format('H\Hi')))
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

    private function createLiveDescription(Live $live, Image $image, int $x): void
    {
        $font = (new Font(u($live->getDescription())->upper()->toString()))
            ->file($this->fontTitle)
            ->size(52)
            ->color('#00153f')
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
