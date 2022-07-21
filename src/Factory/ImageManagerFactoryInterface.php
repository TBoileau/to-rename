<?php

declare(strict_types=1);

namespace App\Factory;

use Intervention\Image\ImageManager;

interface ImageManagerFactoryInterface
{
    public function create(string $driver): ImageManager;
}
