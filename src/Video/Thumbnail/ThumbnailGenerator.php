<?php

declare(strict_types=1);

namespace App\Video\Thumbnail;

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
                u(
                    null !== $video->getLive()
                        ? sprintf(
                            'S%02dE%02d',
                            $video->getLive()->getSeason(),
                            $video->getLive()->getEpisode()
                        )
                        : 'Vidéo'
                )->upper()->toString(),
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
                u(
                    null !== $video->getLive()?->getContent()
                        ? $video->getLive()->getContent()->getVideoTitle()
                        : $video->getTitle()
                )->upper()->wordwrap(20, "\n", false)->toString(),
                918,
                520,
                fn (AbstractFont $font) => $font
                    ->file($this->fonts['thunder'])
                    ->size(128)
                    ->color('#FFFFFF')
                    ->align('left')
                    ->valign('top')
            );

        $categoryImageFile = null === $video->getLive()?->getContent()
            ? sprintf('%s/%s', $this->uploadDir, $video->getLogo())
            : sprintf('%s/%s', $this->uploadDir, $video->getLive()->getContent()::getLogo());

        /** @var array<int, int> $imageInfo */
        $imageInfo = getimagesize($categoryImageFile);

        /**
         * @var int $imageWidth
         * @var int $imageHeight
         */
        [$imageWidth, $imageHeight] = $imageInfo;

        $categoryW = 490;
        $categoryH = $imageHeight * $categoryW / $imageWidth;

        $category = $this->imageManager->make($categoryImageFile)->resize($categoryW, $categoryH);

        $thumbnail->insert($category, 'center-left', 270, intval(round(555 - ($categoryH / 2))));

        $thumbnail->save(sprintf('%s/%s', $this->uploadDir, $video->getThumbnail()));
    }
}
