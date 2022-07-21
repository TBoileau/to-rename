<?php

declare(strict_types=1);

namespace App\Factory;

use Intervention\Image\ImageManager;

final class ImageManagerFactory implements ImageManagerFactoryInterface
{
    public function create(string $driver): ImageManager
    {
        return new ImageManager(['driver' => $driver]);
    }
}
