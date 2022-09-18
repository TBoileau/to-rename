<?php

declare(strict_types=1);

namespace App\Video;

interface CategoryInterface
{
    public function getName(): string;

    public function getImage(): string;
}
