<?php

declare(strict_types=1);

namespace App\ImageManager;

use Intervention\Image\ImageManager;

interface ImageManagerFactoryInterface
{
    public static function create(string $driver): ImageManager;
}
