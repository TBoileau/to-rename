<?php

declare(strict_types=1);

namespace App\ImageManager;

use Intervention\Image\ImageManager;

final class ImageManagerFactory implements ImageManagerFactoryInterface
{
    public static function create(string $driver): ImageManager
    {
        return new ImageManager(['driver' => $driver]);
    }
}
