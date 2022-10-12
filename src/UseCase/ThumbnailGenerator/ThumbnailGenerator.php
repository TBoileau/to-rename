<?php

declare(strict_types=1);

namespace App\UseCase\ThumbnailGenerator;

use App\Doctrine\Entity\Live;
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

    public function generate(Live $live): void
    {
        $thumbnail = $this->imageManager
            ->make($this->thumbnailImage)
            ->text(
                sprintf(
                    'S%02dE%02d',
                    $live->getSeason(),
                    $live->getEpisode()
                ),
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
                u($live->getContent()->getTitle())
                    ->upper()
                    ->wordwrap(20, "\n", false)->toString(),
                918,
                520,
                fn (AbstractFont $font) => $font
                    ->file($this->fonts['thunder'])
                    ->size(128)
                    ->color('#FFFFFF')
                    ->align('left')
                    ->valign('top')
            );

        $categoryImageFile = sprintf(
            '%s/%s',
            $this->uploadDir,
            $live->getContent()->getCategory()->getImage()
        );

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

        $thumbnail->save(sprintf('%s/%s', $this->uploadDir, $live->getThumbnail()));
    }
}
