<?php

declare(strict_types=1);

namespace App\Generator;

use App\Entity\Video;
use Intervention\Image\AbstractFont;
use Intervention\Image\ImageManager;

use function Symfony\Component\String\u;

final class ThumbnailGenerator implements ThumbnailGeneratorInterface
{
    /**
     * @param array<string, string> $fonts
     */
    public function __construct(
        private ImageManager $imageManager,
        private string $thumbnailImage,
        private array $fonts,
        private string $uploadDir
    ) {
    }

    public function generate(Video $video): void
    {
        $thumbnail = $this->imageManager
            ->make($this->thumbnailImage)
            ->text(
                u(sprintf('Episode %d', $video->getEpisode()))->upper()->toString(),
                1120,
                440,
                fn (AbstractFont $font) => $font
                    ->file($this->fonts['thunder'])
                    ->size(96)
                    ->color('#FFFFFF')
                    ->align('center')
                    ->valign('center')
            )
            ->text(
                u($video->getTitle())->upper()->wordwrap(20, "\n", false)->toString(),
                918,
                520,
                fn (AbstractFont $font) => $font
                    ->file($this->fonts['thunder'])
                    ->size(128)
                    ->color('#FFFFFF')
                    ->align('left')
                    ->valign('top')
            );

        $logoImageFile = sprintf('%s/%s', $this->uploadDir, $video->getLogo());

        /** @var array<int, int> $imageInfo */
        $imageInfo = getimagesize($logoImageFile);

        /**
         * @var int $imageWidth
         * @var int $imageHeight
         */
        [$imageWidth, $imageHeight] = $imageInfo;

        $logoW = 490;
        $logoH = $imageHeight * $logoW / $imageWidth;

        $logo = $this->imageManager->make($logoImageFile)->resize($logoW, $logoH);

        $thumbnail->insert($logo, 'center-left', 270, intval(round(555 - ($logoH / 2))));

        $thumbnail->save(sprintf('%s/%s', $this->uploadDir, $video->getThumbnail()));
    }
}
